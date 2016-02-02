<?php
/**
 * User: rbertorelli
 * Date: 1/27/16
 */

namespace App\Services;
use App\Repositories\ReportRepo;
use App\Services\API\MaroApi;
use App\Services\AbstractReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;

/**
 * Class BlueHornetReportService
 * @package App\Services
 */
class MaroReportService extends AbstractReportService 
{

    protected $reportRepo;
    protected $api;

    public function __construct(ReportRepo $reportRepo, MaroApi $api) {
        $this->reportRepo = $reportRepo;
        $this->api = $api;
    }


    public function retrieveApiReportStats($date) {
        $this->api->setDate($date);
        $outputData = array();

        $this->api->constructApiUrl();
        $firstData = $this->api->sendApiRequest();
        $firstData = $this->processGuzzleResult($firstData);

        $outputData = array_merge($outputData, $firstData);

        if (sizeof($firstData) > 0) {
            $pages = (int)$firstData[0]['total_pages'];
            
            if ($pages > 0) {
                $i = 0;
                while ($i <= $pages) {
                    $this->api->constructApiUrl($i);
                    $data = $this->api->sendApiRequest();
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
        $espAccountId = $this->api->getEspAccountId();
        foreach($data as $id => $row) {
            $row['esp_account_id'] = $espAccountId;
            $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($espAccountId, $convertedReport);
            $convertedDataArray[]= $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this->api->getApiName(), $espAccountId, $convertedDataArray));
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

    public function insertCsvRawStats($reports){
        $arrayReportList = array();
        foreach ($reports as $report) {

            try {
                $this->reportRepo->insertStats($this->getAccountName(), $report);
            } catch (\Exception $e){
                throw new \Exception($e->getMessage());
            }

            $arrayReportList[] = $report;
        }

        Event::fire(new RawReportDataWasInserted($this->api->getApiName(),$this->api->getEspAccountId(), $arrayReportList));
    }

}
