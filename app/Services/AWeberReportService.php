<?php
namespace App\Services;

use App\Events\RawReportDataWasInserted;
use App\Exceptions\JobException;
use App\Facades\DeployActionEntry;
use App\Repositories\ReportRepo;
use App\Services\AbstractReportService;
use App\Services\API\AWeberApi;
use App\Services\EmailRecordService;
use App\Services\Interfaces\IDataService;
use Illuminate\Support\Facades\Event;
use Log;

/**
 * Class AWeberReportService
 * @package App\Services
 */
class AWeberReportService extends AbstractReportService implements IDataService
{
    /**
     * AWeberReportService constructor.
     * @param ReportRepo $reportRepo
     * @param $accountNumber
     */

    const DELIVERABLE_LOOKBACK = 2;

    /**
     * AWeberReportService constructor.
     * @param ReportRepo $reportRepo
     * @param AWeberApi $api
     * @param EmailRecordService $emailRecord
     */
    public function __construct(ReportRepo $reportRepo, AWeberApi $api, EmailRecordService $emailRecord)
    {
        parent::__construct($reportRepo, $api, $emailRecord);
    }

    /**
     * @param $date
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function retrieveApiStats($date)
    {
        $date = null; //unfortunately date does not matter here.
        $campaignData = array();
        $campaigns = $this->api->getCampaigns(20);
        foreach ($campaigns as $campaign) {$i++;
            $clickEmail =$this->api->getStateValue($campaign['list_id'], $campaign['internal_id'], "unique_clicks");
            $openEmail = $this->api->getStateValue($campaign['list_id'], $campaign['internal_id'], "unique_opens");
            $row = array_merge($campaign, ["unique_clicks" => $clickEmail, "unique_opens" => $openEmail]);
            $campaignData[] = $row;
        }
        return $campaignData;
    }

    /**
     * @param $xmlData
     */
    public function insertApiRawStats($data)
    {
        $convertedDataArray = [];
        $espAccountId = $this->api->getEspAccountId();
        foreach($data as $row) {
            $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($espAccountId, $convertedReport);
            $convertedDataArray[]= $convertedReport;
        }
        Event::fire(new RawReportDataWasInserted($this, $convertedDataArray));
    }

    public function mapToRawReport($data)
    {
        return array(
            "internal_id" => $data['internal_id'],
            "esp_account_id" => $this->api->getEspAccountId(),
            "info_url"  => $data['info_url'],
            "subject" => $data['subject'],
            "datetime" => $data['sent_at'],
            "total_sent" => $data['total_sent'],
            "total_opens" => $data['total_opens'],
            "total_unsubscribes" => $data['total_unsubscribes'],
            "total_clicks" => $data['total_clicks'],
            "total_undelivered" => $data['total_undelivered'],
            "unique_clicks" => $data['unique_clicks'],
            "unique_opens" => $data['unique_opens'],

        );
    }

    public function mapToStandardReport($data)
    {
        return array(
            'campaign_name' => "",
            'external_deploy_id' => 0,
            'm_deploy_id' => 0,
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => "",
            'datetime' => $data['datetime'],
            'name' => "",
            'subject' => "",
            'from' => "",
            'from_email' => "",
            'delivered' => $data[ 'total_sent' ],
            'bounced' => "",
            'e_opens' => $data[ 'total_opens' ],
            'e_opens_unique' => "",
            'e_clicks' => $data[ 'total_clicks' ],
            'e_clicks_unique' => "",
        );
    }


    public function pushRecords(array $records, $targetId) {}

}
