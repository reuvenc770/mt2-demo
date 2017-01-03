<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:35 PM
 */

namespace App\Repositories;
use App\Models\Interfaces\IReport;
use Log;
class ReportRepo
{
    /**
     * @var IReport
     */
    protected $report;

    public function __construct(IReport $report){
        $this->report = $report;
    }

    public function insertStats($espAccountId, $data) {
        $this->report->updateOrCreate(array("internal_id"=> $data["internal_id"], "esp_account_id" => $espAccountId),$data);
    }

    public function insertCSVStats($espAccountId, $data) {
        $this->report->updateOrCreate(array("campaign_name"=> $data["campaign_name"], "esp_account_id" => $espAccountId),$data);
    }

    public function getCampaigns( $espAccountId , $date ) {
        return $this->report
            ->where( 'created_at' , ">=" , $date )
            ->where( 'esp_account_id' , $espAccountId )
            ->get();
    }

    public function getRunId($espInternalId) {
        if (is_a($this->report, 'App\Models\CampaignerReport')) {
            return $this->report->select('run_id')->where('internal_id', $espInternalId)->first()->run_id;
        }
        else {
            throw new \Exception('Run id accessed by esp without run id.');
        }
    }

    //currently only used by aweber but could be made a bit more generic and work for everyone
    public function updateStatCount($id, $columnName, $value) {
        return $this->report->find($id)->update([$columnName => $value]);
    }

}
