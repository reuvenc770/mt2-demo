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
        #queries table for records that are not transient.
    }

    /**
     *  getFullTransients():
     *      Pulls any possible changes. Has to be used when attribution levels for feeds have changed.
     *      Takes significantly longer to process everything because it pulls millions of records.
     *      The query itself is faster than the optimized query, but must be run hundreds of times.
     *      Order of magnitude: hours
     */

    public function getFullTransients() {
        $attrDb = config('database.connections.attribution.database');

        $union = DB::connection('attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'eca.client_id', 'eca.capture_date', 'art.has_action', 'art.action_expired', DB::raw('aas.trigger_date - INTERVAL 90 DAY as action_datetime'))
                      ->join($attrDb . '.email_client_assignments as eca', 'art.email_id', '=', 'eca.email_id')
                      ->join($attrDb . '.attribution_activity_schedules as aas', 'art.email_id', '=', 'aas.email_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 1)
                      ->where('action_expired', 1)
                      ->where('additional_imports', 1);

        return DB::connection('attribution')->table('attribution_record_truths AS art')
                    ->select('art.email_id', 'eca.client_id', 'eca.capture_date', 'art.has_action', 'art.action_expired', DB::raw('aas.trigger_date - INTERVAL 90 DAY as action_datetime'))
                    ->join($attrDb . '.email_client_assignments as eca', 'art.email_id', '=', 'eca.email_id')
                    ->join($attrDb . '.attribution_activity_schedules as aas', 'art.email_id', '=', 'aas.email_id')
                    ->where('recent_import', 0)
                    ->where('has_action', 0)
                    ->where('additional_imports', 1)
                    ->union($union)
                    ->orderBy('email_id');
    }


    /**
     *  getOptimizedTransients($startDateTime):
     *      Pulls a tiny subset of the above. Processes all records just coming out of 10-day and 90-day windows 
     *      and any that have had any imports since the last time attribution ran. 
     *      Should be fairly quick as the query itself is slower but likely only needs to be run once.
     *      Order of magnitude: minutes 
     */

    public function getOptimizedTransients($startDateTime) {
        
        $attrDb = config('database.connections.attribution.database');
        $dataDb = config('database.connections.mysql.database');

        # These are records that are not protected by the 10-day recent shield, that never had an action, that have subsequent_imports
        # that have a recent import in the past day
        # previous imports are unlikely to change things (they've failed before), but the new one might

        $union1 = DB::connection('attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'eca.client_id', 'eca.capture_date', 'art.has_action', 'art.action_expired', DB::raw('aas.trigger_date - INTERVAL 90 DAY as action_datetime'))
                      ->join($attrDb . '.email_client_assignments as eca', 'art.email_id', '=', 'eca.email_id')
                      ->join($attrDb . '.attribution_activity_schedules as aas', 'art.email_id', '=', 'aas.email_id')
                      ->join("$dataDb.email_client_instances as eci", 'art.email_id', '=', 'eci.email_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 0)
                      ->where('additional_imports', 1)
                      ->groupBy('eca.email_id', 'eca.client_id', 'eca.capture_date', 'art.has_action', 'art.action_expired', 'action_datetime')
                      ->havingRaw("MAX(eci.capture_date) >= '$startDateTime'");

        // These are records that are not protected by the 10-day recent shield, DID have an action but have lost the 90-day shield, 
        // and have subsequent imports, and have a recent import in the past day.
        // Previous imports won't change things (they've failed before), but the new one(s) might
        $union2 = DB::connection('attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'eca.client_id', 'eca.capture_date', 'art.has_action', 'art.action_expired', DB::raw('aas.trigger_date - INTERVAL 90 DAY as action_datetime'))
                      ->join("$attrDb.email_client_assignments as eca", 'art.email_id', '=', 'eca.email_id')
                      ->join("$attrDb.attribution_activity_schedules as aas", 'art.email_id', '=', 'aas.email_id')
                      ->join("$dataDb.email_client_instances as eci", 'art.email_id', '=', 'eci.email_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 1)
                      ->where('action_expired', 1)
                      ->where('additional_imports', 1)
                      ->groupBy('eca.email_id', 'eca.client_id', 'eca.capture_date', 'art.has_action', 'art.action_expired', 'action_datetime')
                      ->havingRaw("MAX(eci.capture_date) >= '$startDateTime'");

        // These are records that have just lost the 90-day shield today and have subsequent imports
        // we need to investigate whether records received during the shielded period can now grab this email
        $union3 = DB::connection('attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'eca.client_id', 'eca.capture_date', 'art.has_action', 'art.action_expired', DB::raw('aas.trigger_date - INTERVAL 90 DAY as action_datetime'))
                      ->join("$attrDb.email_client_assignments as eca", 'art.email_id', '=', 'eca.email_id')
                      ->join("$attrDb.attribution_activity_schedules as aas", 'art.email_id', '=', 'aas.email_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 1)
                      ->where('action_expired', 1)
                      ->where('additional_imports', 1);
                      ->whereRaw("aas.trigger_date = CURDATE()")

        // records that have just come out of the 10-day window, have no actions, and have subsequent imports
        // can subsequent imports during the shielded time now get this email?
        return DB::connection('attribution')->table('attribution_record_truths AS art')
                    ->select('art.email_id', 'eca.client_id', 'eca.capture_date', 'art.has_action', 'art.action_expired', DB::raw('aas.trigger_date - INTERVAL 90 DAY as action_datetime'))
                    ->join("$attrDb.email_client_assignments as eca", 'art.email_id', '=', 'eca.email_id')
                    ->join("$attrDb.attribution_activity_schedules as aas", 'art.email_id', '=', 'aas.email_id')
                    ->join("$attrDb.attribution_expiration_schedules as aes", 'art.email_id', '=', 'aes.email_id')
                    ->where('recent_import', 0)
                    ->where('has_action', 0)
                    ->where('additional_imports', 1)
                    ->whereRaw("aes.trigger_date = CURDATE()")
                    ->union($union1)
                    ->union($union2)
                    ->union($union3)
                    ->orderBy('email_id');
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
        foreach(array_chunk($emails,10000) as $chunk) {
            DB::connection("attribution")->statement(
                "INSERT INTO attribution_record_truths (email_id, recent_import, created_at, updated_at)
            VALUES
                        " . join(' , ', $chunk) . "
            ON DUPLICATE KEY UPDATE
            email_id = email_id, recent_import = recent_import, created_at = created_at, updated_at = updated_at ");
        }
    }

}
