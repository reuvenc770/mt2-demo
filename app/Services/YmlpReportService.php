<?php
/**
 * User: rbertorelli
 * Date: 1/27/16
 */

namespace App\Services;
use App\Facades\Suppression;
use App\Repositories\ReportRepo;
use App\Services\API\YmlpApi;
use App\Services\AbstractReportService;
use League\Flysystem\Exception;
use App\Facades\DeployActionEntry;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;
use App\Models\YmlpCampaign;
use Log;
use App\Repositories\YmlpCampaignRepo;
use Carbon\Carbon;
use App\Services\EmailRecordService;
use App\Exceptions\JobException;

/**
 * Class YmlpReportService
 * @package App\Services
 */
class YmlpReportService extends AbstractReportService implements IDataService {
    protected $reportRepo;
    protected $api;
    protected $actions = ['opens', 'clicks', 'bounces', 'complaints', "deliverable"];
    protected $campaignRepo;
    protected $espAccountId;

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
        $deployId = $this->parseSubID($data['name']);
        return array(
            'campaign_name' => $data['name'],
            'external_deploy_id' => $deployId,
            'm_deploy_id' => $deployId,
            'esp_account_id' => $data['esp_account_id'],
            'esp_internal_id' => $data['internal_id'],
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

    public function getUniqueJobId ( &$processState ) {
        $jobId = ( isset( $processState[ 'jobId' ] ) ? $processState[ 'jobId' ] : '' );

        if (
            !isset( $processState[ 'jobIdIndex' ] )
            || ( isset( $processState[ 'jobIdIndex' ] ) && $processState[ 'jobIdIndex' ] != $processState[ 'currentFilterIndex' ] )
        ) {
            switch ( $processState[ 'currentFilterIndex' ] ) {
                case 1 :
                    $jobId .= ( isset( $processState[ 'campaign' ] ) ? '::Campaign-' . $processState[ 'campaign' ]->esp_internal_id : '' );
                break;

                case 3 :
                    $jobId .= '::Type-' . $processState[ 'recordType' ];
                break;
            }

            $processState[ 'jobIdIndex' ] = $processState[ 'currentFilterIndex' ];
            $processState[ 'jobId' ] = $jobId;
        }

        return $jobId;
    }

    public function getTypeList ( $processState ) {
        $typeList = [ 'opens' , 'clicks', "bounces" ];
        if ($this->emailRecord->withinTwoDays( $this->api->getId(), $processState[ 'campaign' ]->esp_internal_id ) ) {
            $typeList []= 'deliveries';
        }

        return $typeList;
    }

    public function splitTypes ( $processState ) {
        return $processState[ 'typeList' ];
    }

    public function saveRecords(&$processState) {
        $type = "";
        $count = 0;
        $espInternalId = $processState['campaign']->esp_internal_id;
        // sometimes this doesn't work - if we don't have the campaign saved
        $deployId = $processState['campaign']->external_deploy_id; 

        try {
            switch ( $processState[ 'recordType' ] ) {
                case 'opens' :
                    $openData = $this->api->getDeliverableStat('opened', $espInternalId);
                    $count = count($openData);
                    foreach ( $openData as $key => $opener ) {
                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_OPENER ,
                            $opener['Email'] ,
                            $this->api->getId(),
                            $deployId,
                            $espInternalId,
                            Carbon::parse($opener['Timestamp'])
                        );
                    }
                    $type = "open";
                break;

                case 'clicks' :
                    $clickData = $this->api->getDeliverableStat('clicked', $espInternalId);
                    $count = count($clickData);
                    foreach ( $clickData as $key => $clicker ) {
                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_CLICKER ,
                            $clicker['Email'] ,
                            $this->api->getId() ,
                            $deployId,
                            $espInternalId,
                            Carbon::parse($clicker['Timestamp'])
                        );
                    }
                    $type = "click";
                break;

                case 'bounces' :
                    $bounceData = $this->api->getDeliverableStat('bounced', $espInternalId);
                    $count = count($bounceData);
                    foreach ( $bounceData as $key => $bouncer ) {
                       Suppression::recordRawHardBounce($this->api->getId(),$bouncer['Email'],$espInternalId, Carbon::today()->toDateString());
                    }
                    $type = "bounce";
                    break;

                case 'deliveries' :
                    $deliveredData = $this->api->getDeliverableStat('delivered', $espInternalId);
                    $count = count($deliveredData);
                    foreach ( $deliveredData as $key => $clicker ) {
                        $this->emailRecord->recordDeliverable(
                            self::RECORD_TYPE_DELIVERABLE ,
                            $clicker['Email'] ,
                            $this->api->getId() ,
                            $deployId,
                            $espInternalId,
                            "0000-00-00 00:00:00"
                        );
                    }
                    $type = "deliverable";
                    break;
            }
            DeployActionEntry::recordSuccessRun($this->api->getEspAccountId(), $processState[ 'campaign' ]->esp_internal_id, $type );
        } catch ( \Exception $e ) {
            DeployActionEntry::recordFailedRun($this->api->getEspAccountId(), $processState[ 'campaign' ]->esp_internal_id, $type);
            $jobException = new JobException( 'Failed to save records. ' . $e->getMessage() , JobException::NOTICE , $e );
            $jobException->setDelay( 180 );
            throw $jobException;
        }
        return $count;
    }

    public function pullUnsubsEmailsByLookback($lookback){
        $startDate = Carbon::today()->subDay($lookback)->toDateString();
        $endDate = Carbon::today()->toDateString();
        return $this->api->callUnsubApi($startDate,$endDate);
    }

    public function insertUnsubs($data, $espAccountId){
        foreach ($data as $entry){
            Suppression::recordRawUnsub($espAccountId,$entry["EMAIL"],0, Carbon::today()->toDateString());
        }
    }

    public function pushRecords(array $records, $targetId) {}
}
