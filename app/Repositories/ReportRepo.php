<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:35 PM
 */

namespace App\Repositories;
use App\Models\Interfaces\IReport;

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

    public function getStats( $espAccountId , $date ) {
        return $this->report
            ->where( 'updated_at' , ">=" , $date )
            ->where( 'esp_account_id' , $espAccountId )
            ->get();
    }
}
