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

    public function getTransientRecords () {
        $attrDb = config('database.connections.attribution.database');

        $union = DB::connection('attribution')->table('attribution_record_truths AS art')
                      ->select('art.email_id', 'eca.client_id', 'eca.capture_date')
                      ->join($attrDb . '.email_client_assignments as eca', 'art.email_id', '=', 'eca.email_id')
                      ->join($attrDb . '.attribution_levels as al', 'eca.client_id', '=', 'al.client_id')
                      ->where('recent_import', 0)
                      ->where('has_action', 1)
                      ->where('action_expired', 1)
                      ->where('additional_imports', 1);

        return DB::connection('attribution')->table('attribution_record_truths AS art')
                    ->select('art.email_id', 'eca.client_id', 'eca.capture_date')
                    ->join($attrDb . '.email_client_assignments as eca', 'art.email_id', '=', 'eca.email_id')
                    ->join($attrDb . '.attribution_levels as al', 'eca.client_id', '=', 'al.client_id')
                    ->where('recent_import', 0)
                    ->where('has_action', 0)
                    ->where('additional_imports', 1)
                    ->union($union)
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
