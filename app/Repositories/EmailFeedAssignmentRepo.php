<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use App\Models\EmailFeedAssignment;
use App\Models\EmailFeedAssignmentHistory;

use DB;

class EmailFeedAssignmentRepo {
    protected $assignment;
    protected $history;
    private $batchData = [];
    private $batchDataCount = 0;
    const INSERT_THRESHOLD = 10000;

    public function __construct ( EmailFeedAssignment $assignment , EmailFeedAssignmentHistory $history ) {
        $this->assignment = $assignment;
        $this->history = $history;
    }

    public function assignFeed ( $emailId , $feedId , $captureDate ) {
        $tableName = $this->assignment->getTable();

        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                {$tableName} ( email_id , feed_id , capture_date , created_at ,updated_at )
            VALUES
                ( '{$emailId}' , '{$feedId}' , '{$captureDate}' , NOW() , NOW() )
            ON DUPLICATE KEY UPDATE
                feed_id = VALUES( feed_id ) ,
                capture_date = VALUES( capture_date ) ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }

    public function getAssignedFeed ( $emailId , $modelId = null ) {
        $feedId = $this->assignment->where( 'email_id' , $emailId )->pluck( 'feed_id' )->pop();

        if ( is_null( $feedId ) && !is_null( $modelId ) ) {
            $this->assignment->setLiveTable();

            $feedId = $this->assignment->where( 'email_id' , $emailId )->pluck( 'feed_id' )->pop();

            $this->assignment->setModelTable( $modelId );
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
        $this->assignment->setModelTable( $modelId );
    }

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( EmailFeedAssignment::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->bigInteger( 'email_id' )->unsigned();
            $table->integer( 'feed_id' )->unsigned();
            $table->date('capture_date');
            $table->timestamps();

            $table->primary( 'email_id' );
            $table->index( 'feed_id' );
            $table->index( [ 'email_id' , 'feed_id' ] );
        });
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( EmailFeedAssignment::BASE_TABLE_NAME . $modelId );
    }

    public function insertBatch($row) {
        if ($this->batchDataCount >= self::INSERT_THRESHOLD) {

            $this->insertStored();
            $this->batchData = [$this->transformRowToString($row)];
            $this->batchDataCount = 1;
        }
        else {
            $this->batchData[] = $this->transformRowToString($row);
            $this->batchDataCount++;
        }
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['feed_id']) . ','
            . $pdo->quote($row['capture_date']) . ','
            . 'NOW(), NOW())';
    }

    public function insertStored() {
        $this->batchData = implode(', ', $this->batchData);

        DB::connection('attribution')->statement("INSERT INTO email_feed_assignments 
            (email_id, feed_id, capture_date, created_at, updated_at)
            VALUES
            {$this->batchData}
            ON DUPLICATE KEY UPDATE
                email_id = email_id,
                feed_id = VALUES(feed_id),
                capture_date = VALUES(capture_date),
                created_at = created_at,
                updated_at = VALUES(updated_at)");

        $this->batchData = [];
        $this->batchDataCount = 0;
    }

    public function getCaptureDate($emailId) {
        $obj = $this->where('email_id', $emailId)->first();

        if ($obj) {
            return $obj->capture_date;
        }
        return null;
    }
}
