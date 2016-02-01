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
        $this->setDate($date);
        $outputData = array();

        $firstPullUrl = $this->constructApiUrl();
        $firstData = $this->sendApiRequest($firstPullUrl);
        $firstData = $this->processGuzzleResult($firstData);

        $outputData = array_merge($outputData, $firstData);

        if (sizeof($firstData) > 0) {
            $pages = (int)$firstData[0]['total_pages'];
            
            if ($pages > 0) {
                $i = 0;
                while ($i <= $pages) {
                    $url = $this->constructApiUrl($i);
                    $data = $this->sendApiRequest($url);
                    $data = $this->processGuzzleResult($data);
                    $outputData = array_merge($outputData, $data);
                    $i++;
                }
            }
        }
        
        return $outputData;
    }

    protected function processGuzzleResult($data) {
        $data = $data->getBody()->getContents();
        return json_decode($data, true);
    }

    public function insertApiRawStats($data) {
        $convertedDataArray = [];
        $espAccountId = $this->getEspAccountId();
        foreach($data as $id => $row) {
            $row['esp_account_id'] = $espAccountId;
            $convertedReport = $this->mapToRawReport($row);
            $this->reportRepo->insertStats($espAccountId, $convertedReport);
            $convertedDataArray[]= $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this->getApiName(), $espAccountId, $convertedDataArray));
    }

    public function mapToStandardReport($data) {
        return array(
            "internal_id" => $data['internal_id'],
            "esp_account_id" => $data['esp_account_id'],
            "name" => $this->accountName,
            "subject" => '',
            "opens" => $data['open'],
            "clicks" => $data['click']
        ); 
    }

    public function mapToRawReport($data) {
        return array(
            'status' => $data['status'],
            'esp_account_id' => $data['esp_account_id'],
            'internal_id' => (int)$data['campaign_id'],
            'name' => $data['name'],
            'sent' => (int)$data['sent'],
            'delivered' => (int)$data['delivered'],
            'open' => (int)$data['open'],
            'click' => (int)$data['click'],
            'bounce' => (int)$data['bounce'],
            'send_at' => $data['send_at'],
            'sent_at' => $data['sent_at'],
            'maro_created_at' => $data['created_at'],
            'maro_updated_at' => $data['updated_at'],
        );
    }

}
