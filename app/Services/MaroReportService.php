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
        $firstData = $this->sendApiRequest($firstPullUrl);
        $firstData = $this->processGuzzleResult($firstData);

        if (sizeof($firstData) > 0) {
            $pages = (int)$firstData[0]['total_pages'];
            $outputData = array_merge($outputData, $firstData);
            
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
        foreach($data as $id => $row) {
            $row['account_name'] = $this->accountName;
            $row['esp_id'] = $this->espId;
            $row['esp_account_id'] = $this->getEspAccountId();
            $convertedReport = $this->mapToRawReport($row);
            try {
                $this->reportRepo->insertStats($this->getEspAccountId(), $convertedReport);
            }
            catch (Exception $e) {
                throw new \Exception($e->getMessage());
            }
        }

        Event::fire(new RawReportDataWasInserted($this->getApiName(), $this->getEspAccountId(), $convertedReport));
    }

    public function mapToStandardReport($data) {
        return array(
            "internal_id" => $data['campaign_id'],
            "esp_account_id" => $this->getEspAccountId(),
            "name" => $this->accountName,
            "subject" => '',
            "opens" => $data['open'],
            "clicks" => $data['click']
        ); 
    }

    public function mapToRawReport($data) {
        return array(
            'status' => $data['status'],
            'esp_id' => $data['esp_id'],
            'account_name' => $data['account_name'],
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
