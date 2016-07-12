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

    public function getTransientRecords () {
        $union = $this->truth
                      ->select('email_id', 'capture_date')
                      ->join(CAPTURE_DATE_TABLE)
                      ->where('recent_import', 0)
                      ->where('has_action', 1)
                      ->where('action_expired', 1)
                      ->where('additional_imports', 1);

        return $this->truth
                    ->select('email_id', 'capture_date')
                    ->join(CAPTURE_DATE_TABLE)
                    ->where('recent_import', 0)
                    ->where('has_action', 0)
                    ->where('additional_imports', 1)
                    ->union($union)
                    ->get();
    
    }

    public function resetRecord () {
        #resets the record to initial value => expired:false|active:false|has_action:?|additional_imports:?
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
