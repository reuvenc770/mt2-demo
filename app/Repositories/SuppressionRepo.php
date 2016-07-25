<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/12/16
 * Time: 4:00 PM
 */

namespace App\Repositories;

use App\Models\Suppression;
use App\Models\SuppressionReason;
use DB;

class SuppressionRepo
{
    protected $suppressionModel;
    protected $suppressionReason;

    public function __construct(Suppression $suppression, SuppressionReason $reason)
    {
        $this->suppressionModel = $suppression;
        $this->suppressionReason = $reason;
    }

    public function insertSuppression($arrayData){
        $this->suppressionModel->updateOrCreate(["email_address" => $arrayData['email_address'],
            "reason_id" => $arrayData['reason_id']], $arrayData);
    }

    public function getRecordsByDateEspType($typeId, $espAccountId, $date){
       return $this->suppressionModel->select("email_address","reason_id")
                                ->where("type_id",$typeId)
                                ->where("esp_account_id",$espAccountId)
                                ->where("date",$date )
                                ->get();
    }

    public function getRecordsByDateToCurrentEspType($typeId, $espAccountId, $date){
        return $this->suppressionModel->select("email_address","reason_id")
            ->where("type_id",$typeId)
            ->where("esp_account_id",$espAccountId)
            ->where("date",'>=', $date )
            ->get();
    }

    public function getRecordsByDateIntervalEspType($typeId, $espAccountId, $date, $operator) {
        $acceptableOperators = ['<>', '=', '!=', '>', '<', '>=', '<='];
        
        if (!in_array($operator, $acceptableOperators)) {
            throw new \Exception("Operator $operator not valid for dates");
        }

        return $this->suppressionModel->select("email_address","reason_id")
            ->where("type_id",$typeId)
            ->where("esp_account_id",$espAccountId)
            ->where("date", $operator, $date )
            ->get();
    }

    public function getReasonByAccountType($espAccountId, $typeId){
        return $this->suppressionReason->select('suppression_reasons.id')->where('suppression_type',$typeId)
                                ->join('esp_accounts', 'esp_accounts.esp_id', '=','suppression_reasons.esp_id')
                                ->where('esp_accounts.id',$espAccountId)->first();

    }
    public function getAllSuppressionsForEmail($email){
        return $this->suppressionModel->where('email_address', $email)->get();
    }
    public function getReasonList(){
        return $this->suppressionReason->select('id as value' , 'display_status as name')->displayable()->get();
    }

    public function convertReasonFromLegacy($reason){
        return $this->suppressionReason->select('display_status as name')->where('legacy_status',$reason)->first()->name;
    }

    public function getReasonById($reason){
        return $this->suppressionReason->find($reason);
    }

    public function getAllSinceDate($date){
        return $this->suppressionModel->select('email_address')->where('date','>=',$date)->get();
    }

    public function espSuppressionsForDateRange($espId, $lookback) {
        return $this->suppressionModel
                    ->select('email_address')
                    ->join('esp_accounts as eac', 'suppressions.esp_account_id', '=', 'eac.id')
                    ->where('eac.esp_id', $espId)
                    ->where('suppressions.created_at', '>=', DB::raw("CURDATE() - INTERVAL $lookback DAY"))
                    ->get();
    }

    public function getUnsubId() {
        return Suppression::TYPE_UNSUB;
    }

    public function getHardBounceId() {
        return Suppression::TYPE_HARD_BOUNCE;
    }

    public function getComplaintId() {
        return Suppression::TYPE_COMPLAINT;
    }

    public function getSuppressedForDeploys($deploys, $date, $typeId) {
        $schema = config('database.connections.reporting_data.database');
        return $this->suppressionModel
                    ->select('email_address', 'date')
                    ->join($schema . ".standard_reports as sr", 'suppressions.esp_internal_id', '=', 'sr.esp_internal_id')
                    ->whereIn('external_deploy_id', $deploys)
                    ->where('suppressions.date', '>=', $date)
                    ->where('type_id', $typeId)
                    ->get();
    }

}