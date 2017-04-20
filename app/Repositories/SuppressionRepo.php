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
use Carbon\Carbon;

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
        return $this->suppressionModel->with(['espAccount','suppressionReason'])->where('email_address', $email)->get();
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
        return $this->suppressionModel
                    ->selectRaw('distinct(email_address)')
                    ->leftJoin('esp_accounts', 'esp_accounts.id', '=', 'suppressions.esp_account_id')
                    ->whereRaw("((esp_accounts.enable_suppression = 1) OR suppressions.esp_account_id = 0) AND suppressions.created_at >= '$date'");
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

    public function getAllSuppressionsDateRange ( array $dateRange ) {
        return $this->suppressionModel
            ->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] )
            ->get();
    }

    public function getByInternalEmailDate ( $internalEspId , $emailAddress , $date ) {
        $dateRange = [
            "start" => Carbon::parse( $date )->startOfDay()->toDateTimeString() ,
            "end" => Carbon::parse( $date )->endOfDay()->toDateTimeString()
        ];

        return $this->suppressionModel
            ->where( [
                [ 'esp_internal_id' , $internalEspId ] ,
                [ 'email_address' , $emailAddress ]   
            ] )
            ->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] )
            ->get();
    }

    public function returnSuppressedEmails(array $emails) {
        return $this->suppressionModel->whereIn('email_address', $emails)->select('email_address')->get();
    }

    public function getSuppressionTotalsByEspAccount($date) {
        return $this->suppressionModel
                    ->join('esp_accounts as eac', 'suppressions.esp_account_id', '=', 'eac.id')
                    ->join('esps as e', 'eac.esp_id', '=', 'e.id')
                    ->where('suppressions.created_at', '>=', $date)
                    ->selectRaw('e.name as esp_name, eac.account_name, SUM(IF(type_id = 1, 1, 0)) as unsubs, SUM(IF(type_id = 2, 1, 0)) as hardbounces')
                    ->groupBy('e.name', 'eac.account_name')
                    ->get();
    }

    public function getSuppressionTotalsByEsp($espName, $date) {
        return $this->suppressionModel
            ->join('esp_accounts as eac', 'suppressions.esp_account_id', '=', 'eac.id')
            ->join('esps as e', 'eac.esp_id', '=', 'e.id')
            ->where('suppressions.date', '>=', $date)
            ->where('e.name', '=', $espName)
            ->selectRaw('SUM(IF(type_id = 1, 1, 0)) as unsubs, SUM(IF(type_id = 2, 1, 0)) as hardbounces')
            ->first();
    }

    public function getMinIdForDate($date) {
        return $this->suppressionModel->where('created_at', '>=', $date)->min('id');
    }

    public function getMaxId() {
        return $this->suppressionModel->max('id');
    }

    public function nextNRows($startPoint, $offset) {
        return $this->suppressionModel
            ->where('id', '>=', $startPoint)
            ->orderBy('id')
            ->skip($offset)
            ->first()['id'];
    }

    public function pullSuppressionsBetweenIds($start, $end) {
        return $this->suppressionModel->selectRaw("distinct email_address")->whereRaw("id between $start AND $end")->get();
    }

    public function getTotalSinceDate($date) {
        return $this->suppressionModel->where('date', '>=', $date)->count();
    }
}
