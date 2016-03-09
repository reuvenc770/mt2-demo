<?php
/**
 * User: rbertorelli
 * Date: 1/27/16
 */

namespace App\Services;
use App\Repositories\ReportRepo;
use App\Services\API\YmlpApi;
use App\Services\AbstractReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;

/**
 * Class YmlpReportService
 * @package App\Services
 */
class YmlpReportService extends AbstractReportService implements IDataService {
    protected $reportRepo;
    protected $api;
    protected $actions = ['opens', 'clicks', 'bounces', 'complaints', 'unsubscribes'];

    public function __construct(ReportRepo $reportRepo, YmlpApi $api) {
        $this->reportRepo = $reportRepo;
        $this->api = $api;
    }

    public function retrieveApiStats($date) {
        $this->api->setDate($date);
        return $this->api->sendApiRequest();
    }

    public function retrieveDeliveredRecords() {}

    public function insertDeliverableStats() {}

    public function insertApiRawStats($data) {
        $convertedDataArray = [];
        $espAccountId = $this->api->getEspAccountId();
        foreach($data as $id => $row) {
            $row['esp_account_id'] = $espAccountId;
            $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($espAccountId, $convertedReport);
            $convertedDataArray[]= $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this, $convertedDataArray));
    }

    public function mapToStandardReport($data) {
        return array(
            'deploy_id' => $data['name'],
            'sub_id' => $this->parseSubID($data['name']),
            'm_deploy_id' => 0, // temporarily 0 until deploys are created
            'esp_account_id' => $data['esp_account_id'],
            'datetime' => $data['sent_at'],
            'name' => $data['name'],
            'subject' => $data['subject'],
            'from' => $data['from_name'],
            'from_email' => $data['from_email'],
            'e_sent' => $data['recipients'],
            'delivered' => $data['delivered'],
            'bounced' => (int)$data['bounced'],
            'e_opens' => $data['total_opens'],
            'e_opens_unique' => $data['unique_opens'],
            'e_clicks' => $data['total_clicks'],
            'e_clicks_unique' => $data['unique_clicks'],
        );
    }

    public function mapToRawReport($data) {
        return array(


            'esp_account_id' => $data['esp_account_id'],
            'internal_id' => (int)$data['NewsletterID'],
            'name' => 0, // $data['name'],
            'from_name' => $data['FromName'],
            'from_email' => $data['FromEmail'],
            'subject' => $data['Subject'],
            'date' => $data['Date'],
            'groups' => $data['Groups'],
            'filters' => $data['Filters'],
            'recipients' => (int)$data['Recipients'],
            'delivered' => (int)$data['Delivered'],
            'bounced' => (int)$data['Bounced'],
            'total_opens' => (int)$data['TotalOpens'],
            'unique_opens' => (int)$data['UniqueOpens'],
            'total_clicks' => (int)$data['TotalClicks'],
            'unique_clicks' => (int)$data['UniqueClicks'],
            'open_rate' => (float)$data['OpenRate'],
            'click_through_rate' => (float)$data['ClickThroughRate'],
            'forwards' => $data['Forwards'],
            'permalink' => $data['Permalink'],
        );
    }
}