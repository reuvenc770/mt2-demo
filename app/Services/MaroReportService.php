<?php
/**
 * User: rbertorelli
 * Date: 1/27/16
 */

namespace App\Services;
use App\Repositories\ReportRepo;
use App\Services\API\MaroApi;
use App\Services\Interfaces\IAPIReportService;
use App\Services\Interfaces\IReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;

/**
 * Class BlueHornetReportService
 * @package App\Services
 */
class MaroReportService extends MaroApi implements IAPIReportService, IReportService 
{
    public function __construct(ReportRepo $reportRepo, $apiName, $accountNumber) {
        parent::__construct($apiName, $accountNumber);
        $this->reportRepo = $reportRepo;
    }


    public function retrieveApiReportStats($date) {
        $this->date = $date;
        $outputData = array();

        $firstPullUrl = $this->constructApiUrl();
        $firstData = $this->sendApiRequest($firstPullData);

        $firstData = json_decode($firstData, true);
        if (sizeof($firstData) > 0) {
            $pages = 
        }
        
        return $outputData;
    }

    public function insertApiRawStats($data) {}

    public function mapToStandardReport($data) {}

    public function mapToRawReport($data) {
        return array(
            'status' => (string)$data->status,
            'campaign_id' => (int)$data['campaign_id'],
            'name' => $data['name'],
            'sent' => $data['sent'],
            'delivered' => $data['delivered'],
            'open' => $data['open'],
            'click' => $data['click'],
            'bounce' => $data['bounce'],
            'send_at' => $data['send_at'],
            'sent_at' => $data['sent_at'],
            'created_at' => $data['created_at'],
            'updated_at' => $data['updated_at'],
        );
    }

}