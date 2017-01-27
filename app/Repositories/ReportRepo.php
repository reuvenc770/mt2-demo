<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:35 PM
 */

namespace App\Repositories;
use App\Models\Interfaces\IReportMapper;
class ReportRepo
{
    /**
     * @var IReport
     */
    protected $report;

    public function __construct(IReportMapper $report){
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

    public function getAllCampaigns( $espAccountId ) {
        return $this->report
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

    public function updateStatCount($id, $columnName, $value) {
        return $this->report->find($id)->update([$columnName => $value]);
    }

    public function getRowByExternalId($id){
        $result = $this->report->where('internal_id',$id);

        if ( $result->count() > 0 ) {
            return $result->get()[0];
        }

        return null;
    }

    public function getByEspAccountDateSubject($espAccountIds, $dates, $subjects){
        $eloquentObj = $this->report
            ->whereIn( 'esp_account_id', $espAccountIds )
            ->whereIn( \DB::raw( 'DATE( ' . $this->report->getDateFieldName() . ' )' ) , $dates );

        if ( !is_null( $this->report->getSubjectFieldName() ) ) {
            foreach ( $subjects as $currentSubject ) {
                if ( !is_null( $currentSubject ) ) {
                    $eloquentObj = $eloquentObj->orWhere( $this->report->getSubjectFieldName() , 'like' , '%' . $currentSubject . '%' );
                }
            }
        }

        return $eloquentObj->get();
    }

    public function getRawCampaignsFromName($campaignName, $espAccountId){
        return $this->report->where(["message_name" => $campaignName, "esp_account_id" => $espAccountId])->get();
    }

}
