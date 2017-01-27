<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/9/16
 * Time: 2:05 PM
 */

namespace App\Services;
use App\Repositories\ReportRepo;
use App\Services\AbstractReportService;
use App\Services\API\GetResponseApi;
use App\Services\Interfaces\IDataService;
use App\Events\RawReportDataWasInserted;
use Event;
use Log;
use App\Services\EmailRecordService;

class GetResponseReportService extends AbstractReportService implements IDataService
{
    public function __construct(ReportRepo $reportRepo, GetResponseApi $api , EmailRecordService $emailRecord )
    {
        parent::__construct($reportRepo, $api , $emailRecord );
        $this->api->setAction("newsletters");
    }

    public function retrieveApiStats($date)
    {

        try {
            $data = $this->api->setQuery(array('query[createdOn][from]=' => $date, 'query[createdOn][to]=' => $date))->sendApiRequest();
        } catch (\Exception $e){
            Log::error("{GetResponse API Called Failed {$e->getMessage()}");
            throw new \Exception($e->getMessage());
        }
        if (empty($data)){
            return null;
        }
        $campaignData = array();
        foreach ($data as $newsletter) {
            try {
                $campaign = $this->api->sendDirectApiRequest($newsletter['href']);
                $part = array();
                $part['info'] = $campaign;
                $part['fromInfo'] = $this->api->sendDirectApiRequest($campaign['fromField']['href']);
                $part['replyTo'] = $this->api->sendDirectApiRequest($campaign['fromField']['href']);
                $part['stats'] = $this->api->setQuery('?query[groupBy]=total')
                    ->setAction("newsletters/{$newsletter['newsletterId']}/statistics")
                    ->sendApiRequest()[0];
            } catch (\Exception $e){
                Log::error("{GetResponse API Called Failed {$e->getMessage()}");
                throw new \Exception($e->getMessage());
            }
            $campaignData[] = $part;

        }
        return $campaignData;
    }

    public function insertApiRawStats($data)
    {
        $convertedDataArray = [];
        $espAccountId = $this->api->getEspAccountId();
        foreach($data as $row) {

            $row['esp_account_id'] = $espAccountId;
           $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($espAccountId, $convertedReport);
            $convertedDataArray[]= $convertedReport;
        }
        Event::fire(new RawReportDataWasInserted($this, $convertedDataArray));
    }

    public function mapToRawReport($data)
    {

        return array(
            'name' => $data['info']['name'],
            'esp_account_id' => $data['esp_account_id'],
            'subject' => $data['info']['subject'],
            'internal_id' => $data['info']['newsletterId'],
            'info_url' => $data['info']['href'],
            'from_name' => $data['fromInfo']['name'],
            'from_email' => $data['fromInfo']['email'],
            'reply_name' => $data['replyTo']['name'],
            'reply_email' => $data['replyTo']['email'],
            'html' => $data['info']['content']['html'],
            'sent' => $data['stats']['sent'],
            'total_open' => $data['stats']['totalOpened'],
            'unique_open' => $data['stats']['uniqueOpened'],
            'total_click' => $data['stats']['totalClicked'],
            'unique_click' => $data['stats']['uniqueClicked'],
            'unsubscribes' => $data['stats']['unsubscribed'],
            'bounces' => $data['stats']['bounced'],
            'complaints' => $data['stats']['complaints'],
            'sent_on'     => $data['info']['sendOn'],
            'created_on' => $data['info']['createdOn'],

        );
    }

    public function mapToStandardReport($data)
    {
        $deployId = $this->parseSubID($data['name']);
        return array(
            'campaign_name' => $data[ 'name' ],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $this->api->getEspAccountId(),
            'esp_internal_id' => $data['internal_id'],
            'datetime' => $data[ 'sent_on' ],
            'name' => $data[ 'name' ],
            'subject' => $data[ 'subject' ],
            'from' => $data[ 'from_name' ],
            'from_email' => $data[ 'from_email' ],
            'delivered' => $data[ 'sent' ],
            'bounced' => $data['bounces'],
            'e_opens' => $data[ 'total_open' ],
            'e_opens_unique' => $data[ 'unique_open' ],
            'e_clicks' => $data[ 'total_click' ],
            'e_clicks_unique' => $data[ 'unique_click' ],
        );
    }

    public function pushRecords(array $records, $targetId) {}

    public function setRetrieveApiLimit ( $limit ) {}
}
