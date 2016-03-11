<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/11/16
 * Time: 9:13 AM
 */

namespace App\Services;

use App\Repositories\ReportRepo;
use App\Services\API\AWeberApi;
use App\Services\Interfaces\IDataService;
use Event;
use App\Events\RawReportDataWasInserted;
class AWeberReportService extends AbstractReportService implements IDataService
{
    public function __construct(ReportRepo $reportRepo, AWeberApi $api)
    {
        parent::__construct($reportRepo, $api);
    }
    //we may have to use date to hold offset, and build something that queries per page...
    public function retrieveApiStats($date)
    {
        $date = null; //unfortunately date does not matter here.
        $campaignData = array();
        $campaigns = $this->api->getCampaigns(20);
          foreach ($campaigns as $campaign) {
              $clickEmail = $this->api->getStateValue($campaign->id, "unique_clicks");
              $openEmail = $this->api->getStateValue($campaign->id, "unique_opens");
              $row = array(
                  "internal_id" => $campaign->id,
                  "subject" => $campaign->subject,
                  "sent_at" => $campaign->sent_at,
                  "info_url" => $campaign->self_link,
                  "total_sent" => $campaign->total_sent,
                  "total_opens" => $campaign->total_opens,
                  "total_unsubscribes" => $campaign->total_unsubscribes,
                  "total_clicks" => $campaign->total_clicks,
                  "total_undelivered" => $campaign->total_undelivered,
                  "unique_clicks" => $clickEmail,
                  "unique_opens" => $openEmail,
              );
              $campaignData[] = $row;
          }

        return $campaignData;
    }


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
            "sent_at" => $data['sent_at'],
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
            'deploy_id' => "",
            'sub_id' => "",
            'esp_account_id' => $this->api->getEspAccountId(),
            'datetime' => $data[ 'sent_at' ],
            'name' => "",
            'subject' => $data[ 'subject' ],
            'from' => "",
            'from_email' => "",
            'delivered' => $data[ 'total_sent' ],
            'bounced' => $data['total_undelivered'],
            'e_opens' => $data[ 'total_opens' ],
            'e_opens_unique' => $data[ 'unique_opens' ],
            'e_clicks' => $data[ 'total_clicks' ],
            'e_clicks_unique' => $data[ 'unique_clicks' ],
        );
    }
}