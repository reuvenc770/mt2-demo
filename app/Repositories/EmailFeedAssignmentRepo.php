<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Repositories\RepoInterfaces\IAwsRepo;
use App\Repositories\RepoTraits\Batchable;
use App\Repositories\RepoInterfaces\ICanonicalDataSource;

use App\Models\EmailFeedAssignment;
use App\Models\EmailFeedAssignmentHistory;
use App\Models\RedshiftModels\EmailFeedAssignment as RedshiftModel;

use DB;

class EmailFeedAssignmentRepo implements IAwsRepo, ICanonicalDataSource {
    use Batchable;

    protected $model;
    protected $history;

    public function __construct ( EmailFeedAssignment $model , EmailFeedAssignmentHistory $history ) {
        $this->model = $model;
        $this->history = $history;
    }

    public function assignFeed ( $emailId , $feedId , $subscribeDate ) {
        $tableName = $this->model->getTable();

        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                {$tableName} ( email_id , feed_id , subscribe_date , created_at ,updated_at )
            VALUES
                ( '{$emailId}' , '{$feedId}' , '{$subscribeDate}' , NOW() , NOW() )
            ON DUPLICATE KEY UPDATE
                feed_id = VALUES( feed_id ) ,
                subscribe_date = VALUES( subscribe_date ) ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }

    public function getAssignedFeed ( $emailId , $modelId = null ) {
        $feedId = $this->model->where( 'email_id' , $emailId )->pluck( 'feed_id' )->pop();

        if ( is_null( $feedId ) && !is_null( $modelId ) ) {
            $this->model->setLiveTable();

            $feedId = $this->model->where( 'email_id' , $emailId )->pluck( 'feed_id' )->pop();

            $this->model->setModelTable( $modelId );
        }
       
        if ( is_null( $feedId ) ) {
            return 0;
        }

        return $feedId;
    }

    public function recordSwap ( $emailId , $prevFeedId , $newFeedId ) {
        $this->history->create([
            'email_id' => $emailId,
            'prev_feed_id' => $prevFeedId,
            'new_feed_id' => $newFeedId
        ]);
    }

    public function setLevelModel ( $modelId ) {
        $this->model->setModelTable( $modelId );
    }

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( EmailFeedAssignment::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->bigInteger( 'email_id' )->unsigned();
            $table->integer( 'feed_id' )->unsigned();
            $table->date('subscribe_date');
            $table->timestamps();

            $table->primary( 'email_id' );
            $table->index( 'feed_id' );
            $table->index( [ 'email_id' , 'feed_id' ] );
        });
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( EmailFeedAssignment::BASE_TABLE_NAME . $modelId );
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['feed_id']) . ','
            . $pdo->quote($row['subscribe_date']) . ','
            . 'NOW(), NOW())';
    }

    public function buildBatchedQuery($batchData) {
        return "INSERT INTO email_feed_assignments 
            (email_id, feed_id, subscribe_date, created_at, updated_at)
            VALUES
            {$batchData}
            ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = VALUES(feed_id),
                subscribe_date = VALUES(subscribe_date),
                created_at = created_at,
                updated_at = VALUES(updated_at)";
    }

    public function getSubscribeDate($emailId) {
        $obj = $this->model->where('email_id', $emailId)->first();

        if ($obj) {
            return $obj->subscribe_date;
        }
        return null;
    }

    public function getFeedUniques($feedId) {
        $mt2DataSchema = config('database.connections.mysql.database');
        return $this->model
                    ->join("$mt2DataSchema.email_feed_instances as efi", 'email_feed_assignments.email_id', '=', 'efi.email_id')
                    ->selectRaw("efi.email_id, COUNT(*) as count")
                    ->groupBy('efi.email_id')
                    ->havingRaw("COUNT(*) = 0");
    }

    public function extractForS3Upload($startPoint) {
        return $this->model->whereRaw("updated_at > $startPoint");
    }

    public function extractAllForS3() {
        return $this->model->whereRaw("updated_at > CURDATE() - INTERVAL 10 DAY");
    }

    public function specialExtract($data) {
        $feeds = implode(',', $data);
        return $this->model->whereRaw("feed_id in ($feeds)");
    }


    public function mapForS3Upload($row) {
        $pdo = DB::connection('redshift')->getPdo();
        return $pdo->quote($row->email_id) . ','
             . $pdo->quote($row->feed_id) . ','
             . $pdo->quote($row->created_at) . ','
             . $pdo->quote($row->updated_at) . ','
             . $pdo->quote($row->subscribe_date);
    }

    public function getConnection() {
        return $this->model->getConnectionName();
    }

    public function compareSourcesWithField($tableName, $startPoint, $segmentEnd) {
        // empty, but present for interface
        return $this->model
                    ->leftJoin("$tableName as tbl", function($join) {
                        $join->on('email_feed_assignments.email_id', '=', "tbl.email_id");
                        $join->on('email_feed_assignments.feed_id', '=', "tbl.feed_id");
                    })
                    ->whereRaw("email_feed_assignments.email_id BETWEEN {$startPoint} AND {$segmentEnd}")
                    ->whereRaw('email_feed_assignments.updated_at >= CURDATE() - INTERVAL 1 DAY')
                    ->whereRaw("tbl.email_id IS NULL OR email_feed_assignments.subscribe_date <> tbl.subscribe_date")
                    ->selectRaw('email_feed_assignments.email_id, email_feed_assignments.feed_id, email_feed_assignments.subscribe_date')
                    ->get()
                    ->toArray();
    }

    public function compareSources($tableName, $startPoint, $segmentEnd) {
        // empty but required
    }

    public function maxId() {
        return $this->model
                    ->whereRaw('updated_at >= CURDATE() - INTERVAL 1 DAY')
                    ->orderBy('email_id', 'desc')
                    ->first()['email_id'];
    }

    public function nextNRows($startPoint, $offset) {
        return $this->model
            ->where('email_id', '>=', $startPoint)
            ->whereRaw('updated_at >= CURDATE() - INTERVAL 1 DAY')
            ->orderBy('email_id')
            ->skip($offset)
            ->first()['email_id'];

    }

    public function lessThan($startPoint, $endPoint) {
        return $startPoint < $endPoint;
    }

    public function getMinAndMaxIds() {
        $min = $this->model->min('email_id');
        $max = $this->model->max('email_id');
        return [$min, $max];
    }

    public function get($emailId) {
        return $this->model->find($emailId);
    }

    public function getAttributionDist() {
        $output = [];

        $result = $this->model
                    ->selectRaw('feed_id, COUNT(*) as total')
                    ->groupBy('feed_id')
                    ->get();

        foreach($result as $row) {
            $output[$row->feed_id] = $row->total;
        }

        return $output;
    }

    public function matches(RedshiftModel $obj) {
        $result = $this->model->find($obj->email_id);

        if ($result) {
            return $result->feed_id === $obj->feed_id;
        }
        else {
            return false;
        }
    }

}
