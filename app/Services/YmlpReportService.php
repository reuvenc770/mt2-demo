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
use App\Models\YmlpCampaign;
use Log;
use App\Repositories\YmlpCampaignRepo;
use Carbon\Carbon;
use App\Services\EmailRecordService;

/**
 * Class YmlpReportService
 * @package App\Services
 */
class YmlpReportService extends AbstractReportService implements IDataService {
    protected $reportRepo;
    protected $api;
    protected $actions = ['opens', 'clicks', 'bounces', 'complaints', 'unsubscribes'];
    protected $campaignRepo;
    protected $espAccountId;
    protected $dataRetrievalFailed = false;

    public function __construct(ReportRepo $reportRepo, YmlpApi $api, EmailRecordService $emailRecord ) {
        parent::__construct($reportRepo, $api, $emailRecord);
        $this->campaignRepo = new YmlpCampaignRepo(new YmlpCampaign());
        $this->espAccountId = $this->api->getEspAccountId();
    }

    public function retrieveApiStats($date) {
        $this->api->setDate($date);
        $data = $this->api->sendApiRequest();
        return $data;
    }

    public function insertApiRawStats($data) {
        echo "Running insert" . PHP_EOL;
        $convertedDataArray = [];
        foreach($data as $id => $row) {
            $row['esp_account_id'] = $this->espAccountId;
            $convertedReport = $this->mapToRawReport($row);
            $this->insertStats($this->espAccountId, $convertedReport);
            $convertedDataArray[]= $convertedReport;
        }
        Event::fire(new RawReportDataWasInserted($this, $convertedDataArray));
    }

    private function parseDate($date) {
        $formattedDate = Carbon::parse($date)->toDateString();
        return $formattedDate;
    }

    private function getMtCampaignName($espAccountId, $date) {
        $date = $this->parseDate($date);
        $output = $this->campaignRepo->getMtCampaignNameForAccountAndDate($espAccountId, $date);
        echo "Account id: $espAccountId, date: $date" . PHP_EOL;
        echo "output: $output" . PHP_EOL;
        return $output;
    }

    public function mapToStandardReport($data) {
        return array(
            'deploy_id' => $data['name'],
            'sub_id' => $this->parseSubID($data['name']),
            'm_deploy_id' => 0, // temporarily 0 until deploys are created
            'esp_account_id' => $data['esp_account_id'],
            'datetime' => $data['date'],
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
            'name' => $this->getMtCampaignName($data['esp_account_id'], $data['Date']),
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

    public function getUniqueJobId ( $processState ) {
        if ( isset( $processState[ 'campaign' ]->internal_id ) && !isset( $processState[ 'recordType' ] ) ) {
            return '::Campaign' . $processState[ 'campaign' ]->internal_id;
        } elseif ( isset( $processState[ 'campaign' ]->internal_id ) && isset( $processState[ 'recordType' ] ) ) {
            return '::Campaign' . $processState[ 'campaign' ]->internal_id . '::' . $processState[ 'recordType' ];
        } else {
            return '';
        }
    }

    public function splitTypes () {
        return [ 'opens' , 'clicks' ];
    }

    public function saveRecords(&$processState) {
        $campaignId = $processState['campaign']->internal_id;

        switch ( $processState[ 'recordType' ] ) {
            case 'opens' :
                try {
                    $openData = $this->api->getDeliverableStat('opened', $campaignId);
                } catch ( \Exception $e ) {
                    Log::error( 'Failed to retrieve open report. ' . $e->getMessage() );

                    $this->processState[ 'delay' ] = 180;

                    $this->dataRetrievalFailed = true;

                    return;
                }

                foreach ( $openData as $key => $opener ) {
                    $this->emailRecord->recordDeliverable(
                        self::RECORD_TYPE_OPENER ,
                        $opener['Email'] ,
                        $this->api->getId() ,
                        $campaignId,
                        $opener['Timestamp']
                    );
                }
            break;

            case 'clicks' :
                try {
                    $clickData = $this->api->getDeliverableStat('clicked', $campaignId);
                } catch ( \Exception $e ) {
                    Log::error( 'Failed to retrieve click report. ' . $e->getMessage() );

                    $this->processState[ 'delay' ] = 180;

                    $this->dataRetrievalFailed = true;

                    return;
                }

                foreach ( $clickData as $key => $clicker ) {
                    $this->emailRecord->recordDeliverable(
                        self::RECORD_TYPE_CLICKER ,
                        $clicker['Email'] ,
                        $this->api->getId() ,
                        $campaignId,
                        $clicker['Timestamp']
                    );
                }
            break;
        }
    }

    public function shouldRetry () {
        return $this->dataRetrievalFailed;
    }

    public function getDeliveredRecords ( $action, $newsletterId ) {
        $outputData = array();

        $dataFound = true;
        $page = 1;

        while ($dataFound) {
            $data = $this->api->callDeliverableApiCall($action, $newsletterId, $page);

            $data = $this->processGuzzleResult($data);

            if (empty($data)) {
                $dataFound = false;
            }
            else {
                $outputData = array_merge($outputData, $data);
                $page++;
            }
        }       

        return $outputData;
    }

}
