<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionRecordTruth;
use App\Repositories\RepoTraits\Batchable;
use DB;

class AttributionRecordTruthRepo {
    use Batchable;

    protected $model;

    public function __construct ( AttributionRecordTruth $model ) {
        $this->model = $model;
    }

    public function getAssignedRecords () {
        return DB::connection( 'attribution' )->table( 'attribution_record_truths' )
                    ->select( 'email_id' )
                    ->where( 'recent_import' , 1 )
                    ->orWhere( [
                        [ 'recent_import' , 0 ] ,
                        [ 'has_action' , 1 ] ,
                        [ 'action_expired' , 0 ]
                    ] )
                    ->orWhere( 'additional_imports' , 0 )
                    ->get();
    }

    /**
     *  getFullTransients():
     *      Pulls any possible changes. Has to be used when attribution levels for feeds have changed.
     *      Takes significantly longer to process everything because it pulls millions of records.
     *      The query itself is faster than the optimized query, but must be run many more times.
     */

    public function getFullTransientsBetweenIds($remainder, $startPoint, $endPoint) {
        $attrDb = config('database.connections.slave_attribution.database');

        return DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                    ->select('art.email_id', DB::raw('IFNULL(efa.feed_id, 0) as feed_id'), 'efa.subscribe_date', 'art.has_action', 'art.action_expired', DB::raw('IFNULL(al.level, 0) as level'))
                    ->leftJoin($attrDb . '.email_feed_assignments as efa', 'art.email_id', '=', 'efa.email_id')
                    ->leftJoin($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                    ->whereBetween('art.email_id', $startPoint, $endPoint)
                    ->where('recent_import', 0)
                    ->where('has_action', 0)
                    ->whereRaw('action_expired IN (1,0)')
                    ->where('additional_imports', 1)
                    ->whereRaw("art.email_id % 5 = $remainder")
                    ->orderBy('email_id')
                    ->get();
    }


    /**
     *  getOptimizedTransients($startDateTime):
     *      Pulls a tiny subset of the above. Processes all records just coming out of 15-day window without actions 
     *      and any that have had any imports since the last time attribution ran. 
     *      Should be fairly quick as the query itself is slower but likely only needs to be run once.
     */

    public function getOptimizedTransientsBetweenIds($startDateTime, $remainder, $startPoint, $endPoint) {
        $attrDb = config('database.connections.slave_attribution.database');
        $dataDb = config('database.connections.slave_data.database');

        // These are records that are not protected by the 10-day recent shield, that never had an action, that have subsequent_imports,
        // whose trigger date passed before the previous run (and thus have hit case 4), that have a recent import in the past day
        // previous imports are unlikely to change things (they've failed before), but the new one might
        // We can set the starting "subscribe_date" to the startDateTime because we only want instances after that time
        // (and we start our search for candidates starting on the capture date)
        $union = DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'efa.feed_id', DB::raw("'$startDateTime' as subscribe_date"), 'art.has_action', 'art.action_expired', 'al.level')
                      ->join($attrDb . '.email_feed_assignments as efa', 'art.email_id', '=', 'efa.email_id')
                      ->join("$dataDb.email_feed_instances as efi", 'art.email_id', '=', 'efi.email_id')
                      ->join($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                      ->join("$attrDb.attribution_expiration_schedules as aes", 'art.email_id', '=', 'aes.email_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 0)
                      ->where('additional_imports', 1)
                      ->whereRaw('action_expired IN (1,0)')
                      ->where('aes.trigger_date', '<', $startDateTime)
                      ->whereRaw("art.email_id % 5 = $remainder")
                      ->whereBetween('art.email_id', $startPoint, $endPoint)
                      ->groupBy('efa.email_id', 'efa.feed_id', 'efa.subscribe_date', 'art.has_action', 'art.action_expired')
                      ->havingRaw("MAX(efi.subscribe_date) >= '$startDateTime'");

        // records that have just come out of the 10-day window, have no actions, and have subsequent imports
        // can subsequent imports during the shielded time now get this email?
        return DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                    ->select('art.email_id', 'efa.feed_id', 'efa.subscribe_date', 'art.has_action', 'art.action_expired', 'al.level')
                    ->join("$attrDb.email_feed_assignments as efa", 'art.email_id', '=', 'efa.email_id')
                    ->join("$attrDb.attribution_expiration_schedules as aes", 'art.email_id', '=', 'aes.email_id')
                    ->join($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                    ->where('recent_import', 0)
                    ->where('has_action', 0)
                    ->where('additional_imports', 1)
                    ->whereBetween('aes.trigger_date', [$startDateTime, DB::raw("CURDATE() + INTERVAL 1 HOUR")])
                    ->whereRaw('action_expired IN (0,1)')
                    ->whereRaw("art.email_id % 5 = $remainder")
                    ->whereBetween('art.email_id', $startPoint, $endPoint)
                    ->unionAll($union)
                    ->orderBy('email_id')
                    ->get();
    }

    public function maxId() {
        return $this->model->max('email_id');
    }

    public function minId() {
        return $this->model->min('email_id');
    }

    public function nextNRows($startPoint, $offset) {
        return $this->model
            ->where('email_id', '>=', $startPoint)
            ->orderBy('email_id')
            ->skip($offset)
            ->first()['email_id'];
    }

    public function nextNRowsForAttribution($feedId, $startPoint, $offset) {
        return $this->model
            ->where('email_id', '>=', $startPoint)
            ->where('feed_id', $feedId)
            ->orderBy('email_id')
            ->skip($offset)
            ->first()['email_id'];
    }

    public function getFeedAttributionsBetweenIds($feedId, $remainder, $startId, $endId) {
        $attrDb = config('database.connections.slave_attribution.database');
        $dataDb = config('database.connections.slave_data.database');
        // Need to pick up any imports ever
        // It's a bit more difficult to get when an action happened ... 

        return DB::connection('slave_attribution')
                ->table('attribution_record_truths as art')
                ->join("$dataDb.email_feed_instances as efi", 'art.email_id', '=', 'efi.email_id')
                ->join("$attrDb.attribution_levels as al", 'efi.feed_id', '=', 'al.feed_id')
                ->where('efi.feed_id', $feedId)
                ->whereRaw("art.email_id % 5 = $remainder")
                ->whereBetween('art.email_id', $startId, $endId)
                ->select('art.email_id', 'efa.feed_id', 'efi.subscribe_date', 'has_action', 'action_expired', 'al.level')
                ->get();
    }

    public function resetRecord () {
        #resets the record to initial value => expired:false|active:false|has_action:?|additional_imports:?
    }

    public function setRecord($emailId, $recentImport, $hasAction, $actionExpired, $additionalImports) {
        return $this->model->where("email_id", $emailId)->update([
            'recent_import' => $recentImport,
            'has_action' => $hasAction,
            'action_expired' => $actionExpired,
            'additional_imports' => $additionalImports
        ]);
    }

    public function setField($emailId, $field, $value){
        return $this->model->where("email_id", $emailId)->update(array($field =>$value));
    }

    public function bulkSetField($emails, $field, $value){

        return $this->model->whereIn("email_id", $emails)->update(array($field =>$value));
    }

    public function insert($emailId){
        return $this->model->create(["email_id" => $emailId, "recent_import" => true]);
    }

    public function bulkInsert($emails){
            DB::connection("attribution")->statement(
                "INSERT INTO attribution_record_truths (email_id, recent_import, created_at, updated_at)
            VALUES
                        " . join(' , ', $emails) . "
            ON DUPLICATE KEY UPDATE
            email_id = email_id, recent_import = VALUES(recent_import), created_at = created_at, updated_at = VALUES(updated_at) ");
    }

    private function buildBatchedQuery($data) {
        return "INSERT INTO
            attribution_record_truths ( email_id , recent_import , has_action , created_at , updated_at)
        VALUES

            $data

        ON DUPLICATE KEY UPDATE
            email_id = email_id ,
            recent_import = VALUES(recent_import),
            has_action = VALUES( has_action ) ,
            created_at = created_at,
            updated_at = VALUES( updated_at )";
    }

    private function transformRowToString($row) {
        $pdo = DB::connection()->getPdo();

        return '('
            . $pdo->quote($row['email_id']) . ','
            . $pdo->quote($row['recent_import']) . ','
            . $pdo->quote($row['has_action']) . ','
            . 'NOW() , NOW())';
    }

    public function addNewRows(array $rows) {
        $emails = [];

        foreach($rows as $row) {
            $emails[] = '(' . $row['email_id'] . ', 0, NOW(), NOW())';
        }

        $this->bulkInsert($emails);
    }

    public function getTableName() {
        return config('database.connections.attribution.database') . '.' . $this->model->getTable();
    }
}
