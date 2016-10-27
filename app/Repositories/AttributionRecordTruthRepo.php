<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionRecordTruth;
use DB;

class AttributionRecordTruthRepo {

    protected $truth;

    public function __construct ( AttributionRecordTruth $truth ) {
        $this->truth = $truth;
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

    public function getFullTransients($remainder) {
        $attrDb = config('database.connections.slave_attribution.database');

        $union = DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', DB::raw('IFNULL(efa.feed_id, 0) as feed_id'), 'efa.capture_date', 'art.has_action', 'art.action_expired', DB::raw('IFNULL(al.level, 0) as level'))
                      ->leftJoin($attrDb . '.email_feed_assignments as efa', 'art.email_id', '=', 'efa.email_id')
                      ->leftJoin($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 1)
                      ->where('action_expired', 1)
                      ->where('additional_imports', 1)
                      ->whereRaw("art.email_id % 5 = $remainder");

        return DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                    ->select('art.email_id', DB::raw('IFNULL(efa.feed_id, 0) as feed_id'), 'efa.capture_date', 'art.has_action', 'art.action_expired', DB::raw('IFNULL(al.level, 0) as level'))
                    ->leftJoin($attrDb . '.email_feed_assignments as efa', 'art.email_id', '=', 'efa.email_id')
                    ->leftJoin($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                    ->where('recent_import', 0)
                    ->where('has_action', 0)
                    ->whereRaw('action_expired IN (0,1)')
                    ->where('additional_imports', 1)
                    ->whereRaw("art.email_id % 5 = $remainder")
                    ->unionAll($union)
                    ->orderBy('email_id');

        /*
        return DB::connection('slave_attribution')->select("
            SELECT
                art.email_id,
                IFNULL(efa.feed_id, 0) AS feed_id, 
                efa.capture_date,
                art.has_action,
                art.action_expired,
                IFNULL(al.level, 0) AS level

            FROM
                $attrDb.attribution_record_truths as art
                left join $attrDb.email_feed_assignments as efa ON art.email_id = efa.email_id
                left join $attrDb.attribution_levels as al on efa.feed_id = al.feed_id
            WHERE
                recent_import = 0
                AND
                has_action = 1
                AND
                action_expired = 1
                AND
                additional_imports = 1
                AND
                art.email_id % 5 = $remainder
                
            UNION ALL
                
            SELECT
                art.email_id,
                IFNULL(efa.feed_id, 0) AS feed_id, 
                efa.capture_date,
                art.has_action,
                art.action_expired,
                IFNULL(al.level, 0) AS level

            FROM
                $attrDb.attribution_record_truths as art
                left join $attrDb.email_feed_assignments as efa ON art.email_id = efa.email_id
                left join $attrDb.attribution_levels as al on efa.feed_id = al.feed_id
            WHERE
                recent_import = 0
                AND
                has_action = 0
                AND
                action_expired IN (0,1)
                AND
                additional_imports = 1
                AND
                art.email_id % 5 = $remainder
        ");
        */
    }


    /**
     *  getOptimizedTransients($startDateTime):
     *      Pulls a tiny subset of the above. Processes all records just coming out of 10-day and 90-day windows 
     *      and any that have had any imports since the last time attribution ran. 
     *      Should be fairly quick as the query itself is slower but likely only needs to be run once.
     */

    public function getOptimizedTransients($startDateTime, $remainder) {
        
        $attrDb = config('database.connections.slave_attribution.database');
        $dataDb = config('database.connections.slave_data.database');

        // These are records that are not protected by the 10-day recent shield, that never had an action, that have subsequent_imports,
        // whose trigger date passed before the previous run (and thus have hit case 4), that have a recent import in the past day
        // previous imports are unlikely to change things (they've failed before), but the new one might
        // We can set the starting "capture_date" to the startDateTime because we only want instances after that time
        // (and we start our search for candidates starting on the capture date)
        $union1 = DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'efa.feed_id', DB::raw("'$startDateTime' as capture_date"), 'art.has_action', 'art.action_expired', 'al.level')
                      ->join($attrDb . '.email_feed_assignments as efa', 'art.email_id', '=', 'efa.email_id')
                      ->join("$dataDb.email_feed_instances as efi", 'art.email_id', '=', 'efi.email_id')
                      ->join($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                      ->join("$attrDb.attribution_expiration_schedules as aes", 'art.email_id', '=', 'aes.email_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 0)
                      ->where('additional_imports', 1)
                      ->whereRaw('action_expired IN (0,1)')
                      ->where('aes.trigger_date', '<', $startDateTime)
                      ->whereRaw("art.email_id % 5 = $remainder")
                      ->groupBy('efa.email_id', 'efa.feed_id', 'efa.capture_date', 'art.has_action', 'art.action_expired')
                      ->havingRaw("MAX(efi.capture_date) >= '$startDateTime'");

        // These are records that are not protected by the 10-day recent shield, DID have an action but have lost the 90-day shield, 
        // and have subsequent imports, whose action trigger date passed before the prior run, and have a recent import in the past day.
        // Previous imports won't change things (they've failed before), but the new one(s) might
        // See above for reasoning about using $startDateTime as capture_date
        $union2 = DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'efa.feed_id', DB::raw("'{$startDateTime}' as capture_date"), 'art.has_action', 'art.action_expired', 'al.level')
                      ->join("$attrDb.email_feed_assignments as efa", 'art.email_id', '=', 'efa.email_id')
                      ->join("$dataDb.email_feed_instances as efi", 'art.email_id', '=', 'efi.email_id')
                      ->join($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                      ->join("$attrDb.attribution_activity_schedules as aas", 'art.email_id', '=', 'aas.email_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 1)
                      ->where('action_expired', 1)
                      ->where('additional_imports', 1)
                      ->where('aas.trigger_date', '<', $startDateTime)
                      ->whereRaw("art.email_id % 5 = $remainder")
                      ->groupBy('efa.email_id', 'efa.feed_id', 'efa.capture_date', 'art.has_action', 'art.action_expired')
                      ->havingRaw("MAX(efi.capture_date) >= '$startDateTime'");

        // These are records that have just lost the 90-day shield today and have subsequent imports
        // we need to investigate whether records received during the shielded period can now grab this email
        $union3 = DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'efa.feed_id', 'efa.capture_date', 'art.has_action', 'art.action_expired', 'al.level')
                      ->join("$attrDb.email_feed_assignments as efa", 'art.email_id', '=', 'efa.email_id')
                      ->join("$attrDb.attribution_activity_schedules as aas", 'art.email_id', '=', 'aas.email_id')
                      ->join($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 1)
                      ->where('action_expired', 1)
                      ->where('additional_imports', 1)
                      ->whereRaw("art.email_id % 5 = $remainder")
                      ->whereBetween('aas.trigger_date', [$startDateTime, DB::raw("CURDATE() + INTERVAL 1 HOUR")]);

        // records that have just come out of the 10-day window, have no actions, and have subsequent imports
        // can subsequent imports during the shielded time now get this email?
        return DB::connection('slave_attribution')->table('attribution_record_truths AS art')
                    ->select('art.email_id', 'efa.feed_id', 'efa.capture_date', 'art.has_action', 'art.action_expired', 'al.level')
                    ->join("$attrDb.email_feed_assignments as efa", 'art.email_id', '=', 'efa.email_id')
                    ->join("$attrDb.attribution_expiration_schedules as aes", 'art.email_id', '=', 'aes.email_id')
                    ->join($attrDb . '.attribution_levels as al', 'efa.feed_id', '=', 'al.feed_id')
                    ->where('recent_import', 0)
                    ->where('has_action', 0)
                    ->where('additional_imports', 1)
                    ->whereBetween('aes.trigger_date', [$startDateTime, DB::raw("CURDATE() + INTERVAL 1 HOUR")])
                    ->whereRaw('action_expired IN (0,1)')
                    ->whereRaw("art.email_id % 5 = $remainder")
                    ->unionAll($union1)
                    ->unionAll($union2)
                    ->unionAll($union3)
                    ->orderBy('email_id');
    }

    public function getFeedAttributions($feedId, $remainder) {
        $attrDb = config('database.connections.slave_attribution.database');
        $dataDb = config('database.connections.slave_data.database');
        // Need to pick up any imports ever, I think, and not just the one prior to the attribution.
        // It's a bit more difficult to get when an action happened ... 

        return DB::connection('slave_attribution')
                ->table('attribution_record_truths as art')
                ->join("$dataDb.email_feed_instances as efa", 'art.email_id', '=', 'efa.email_id')
                ->join("$attrDb.attribution_levels as al", 'efa.feed_id', '=', 'al.feed_id')
                ->where('efa.feed_id', $feedId)
                ->whereRaw("art.email_id % 5 = $remainder")
                ->select('art.email_id', 'efa.feed_id', DB::raw('"2000-01-01" as capture_date'), DB::raw('0 as has_action'), DB::raw('0 as action_expired'), 'al.level');
    }

    public function resetRecord () {
        #resets the record to initial value => expired:false|active:false|has_action:?|additional_imports:?
    }

    public function setRecord($emailId, $recentImport, $hasAction, $actionExpired, $additionalImports) {
        return $this->truth->where("email_id", $emailId)->update([
            'recent_import' => $recentImport,
            'has_action' => $hasAction,
            'action_expired' => $actionExpired,
            'additional_imports' => $additionalImports
        ]);
    }

    public function setField($emailId, $field, $value){
        return $this->truth->where("email_id", $emailId)->update(array($field =>$value));
    }

    public function bulkSetField($emails, $field, $value){

        return $this->truth->whereIn("email_id", $emails)->update(array($field =>$value));
    }

    public function insert($emailId){
        return $this->truth->create(["email_id" => $emailId, "recent_import" => true]);
    }

    public function bulkInsert($emails){
            DB::connection("attribution")->statement(
                "INSERT INTO attribution_record_truths (email_id, recent_import, created_at, updated_at)
            VALUES
                        " . join(' , ', $emails) . "
            ON DUPLICATE KEY UPDATE
            email_id = email_id, recent_import = recent_import, created_at = created_at, updated_at = updated_at ");
    }

}
