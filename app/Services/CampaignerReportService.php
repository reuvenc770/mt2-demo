<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/20/16
 * Time: 10:28 AM
 */

namespace App\Services;


use App\Library\Campaigner\CampaignManagement;
use App\Library\Campaigner\ContactManagement;
use App\Library\Campaigner\DownloadReport;
use App\Library\Campaigner\RunReport;
use App\Repositories\ReportRepo;
use App\Services\API\Campaigner;
use App\Services\API\CampaignerApi;
use App\Services\AbstractReportService;
use App\Library\Campaigner\DateTimeFilter;
use App\Library\Campaigner\GetCampaignRunsSummaryReport;
use Carbon\Carbon;
use App\Events\RawReportDataWasInserted;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use App\Services\Interfaces\IDataService;
use App\Services\EmailRecordService;


/**
 * Class CampaignerReportService
 * @package App\Services
 */
class CampaignerReportService extends AbstractReportService implements IDataService
{

    CONST NO_CAMPAIGNS = 'M_4.1.1.1_NO-CAMPAIGNRUNS-FOUND';
    /**
     * @var string
     */

    /**
     * BlueHornetReportService constructor.
     * @param ReportRepo $reportRepo
     * @param $accountNumber
     */
    public function __construct(ReportRepo $reportRepo, CampaignerApi $api , EmailRecordService $emailRecord )
    {
        parent::__construct($reportRepo, $api , $emailRecord);
    }

    /**
     * @param $data
     * @throws \Exception
     */
    public function insertApiRawStats($data)
    {
        $arrayReportList = array();
        $espAccountId = $this->api->getEspAccountId();

        if (count($data->getCampaign()) > 1) {  //another dumb check
            foreach ($data->getCampaign() as $report) {
                $convertedReport = $this->mapToRawReport($report);
                $this->insertStats($espAccountId, $convertedReport);
                $arrayReportList[] = $convertedReport;
            }
        } else {
            $convertedReport = $this->mapToRawReport($data->getCampaign());
            $this->insertStats($espAccountId, $convertedReport);
            $arrayReportList[] = $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this, $arrayReportList));
    }

    /**
     * @param \App\Library\Campaigner\Campaign $report
     * @return array
     */
    public function mapToRawReport($report)
    {
        $keys = array('sent', 'delivered', 'hard_bounces', 'soft_bounces', 'spam_bounces', 'opens', 'clicks', 'unsubs', 'spam_complaints', 'run_id');
        $emailStats = array_fill_keys($keys, 0);
        //Dumb logic because of how things are nested
        $camaignRuns = $report->getCampaignRuns();

        $runs = $camaignRuns->getCampaignRun();
        if (count($runs) > 1) {  // Campaigner descides to not return array if its only 1 element
            foreach ($runs as $run) {
                $domains = $run->getDomains();
                $domain = $domains->getDomain();
                $deliveryResults = $domain->getDeliveryResults();
                $emailStats['sent'] += $deliveryResults->getSent();
                $emailStats['delivered'] += $deliveryResults->getDelivered();
                $emailStats['hard_bounces'] += $deliveryResults->getHardBounces();
                $emailStats['soft_bounces'] += $deliveryResults->getSoftBounces();
                $emailStats['spam_bounces'] += $deliveryResults->getSpamBOunces();
                $activityResults = $domain->getActivityResults();
                $emailStats['opens'] += $activityResults->getOpens();
                $emailStats['clicks'] += $activityResults->getClicks();
                $emailStats['unsubs'] += $activityResults->getUnsubscribes();
                $emailStats['spam_complaints'] += $activityResults->getSpamComplaints();
                $emailStats['run_id'] = $run->getId();
            }
        } else {
            $domains = $runs->getDomains();
            $domain = $domains->getDomain();
            $deliveryResults = $domain->getDeliveryResults();
            $emailStats['sent'] = $deliveryResults->getSent();
            $emailStats['delivered'] = $deliveryResults->getDelivered();
            $emailStats['hard_bounces'] = $deliveryResults->getHardBounces();
            $emailStats['soft_bounces'] = $deliveryResults->getSoftBounces();
            $emailStats['spam_bounces'] = $deliveryResults->getSpamBOunces();

            $activityResults = $domain->getActivityResults();
            $emailStats['opens'] = $activityResults->getOpens();
            $emailStats['clicks'] = $activityResults->getClicks();
            $emailStats['unsubs'] = $activityResults->getUnsubscribes();
            $emailStats['spam_complaints'] = $activityResults->getSpamComplaints();
            $emailStats['run_id'] = $runs->getId();
        }

        return array(
            'internal_id' => $report->getId(),
            'name' => $report->getName(),
            'esp_account_id' => $this->api->getEspAccountId(),
            'subject' => $report->getSubject(),
            'from_name' => $report->getFromName(),
            'from_email' => $report->getFromEmail(),
            'sent' => $emailStats['sent'],
            'delivered' => $emailStats['delivered'],
            'hard_bounces' => $emailStats['hard_bounces'],
            'soft_bounces' => $emailStats['soft_bounces'],
            'spam_bounces' => $emailStats['spam_bounces'],
            'opens' => $emailStats['opens'],
            'clicks' => $emailStats['clicks'],
            'unsubs' => $emailStats['unsubs'],
            'spam_complaints' => $emailStats['spam_complaints'],
            'run_id' => $emailStats['run_id']
        );

    }

    //we should make a function to return what a standard report is
    /**
     * @param $report
     * @return array
     */
    public function mapToStandardReport($report)
    {
        return array(

            'deploy_id' => $report['name'],
            'sub_id' => $this->parseSubID($report['name']),
            'esp_account_id' => $report['esp_account_id'],
            'datetime' => '0000-00-00', //$report[''],
            'name' => $report['name'],
            'subject' => $report['subject'],
            'from' => $report['from_name'],
            'from_email' => $report['from_email'],
            'e_sent' => $report['sent'],
            'delivered' => $report['delivered'],
            'bounced' => (int)$report['hard_bounces'],
            'optouts' => $report['unsubs'],
            'e_opens' => $report['opens'],
            'e_clicks' => $report['clicks']
        );
    }


    /**
     * @param $date
     * @return \App\Library\Campaigner\ArrayOfCampaign
     * @throws \Exception
     */
    public function retrieveApiStats($date)
    {
        $dateObject = Carbon::createFromTimestamp(strtotime($date));
        $manager = new CampaignManagement();
        $dateFilter = new DateTimeFilter();
        $dateFilter->setFromDate($dateObject->startOfDay());
        $dateFilter->setToDate($dateObject->endOfDay());
        $params = new GetCampaignRunsSummaryReport($this->api->getAuth(), null, false, $dateFilter);
        $results = $manager->GetCampaignRunsSummaryReport($params);
        if($this->checkforHeaderFail($manager,"retrieveApiStats"))
        {
            return null;
        }
        return $results->getGetCampaignRunsSummaryReportResult();
    }


    public function createCampaignReport($reportNumber){
        $manager = new ContactManagement();
        $searchQuery = $this->api->buildCampaignSearchQuery($reportNumber);
        $report = new RunReport($this->api->getAuth(), $searchQuery);
        $results = $manager->RunReport($report)->getRunReportResult();

        if($this->checkforHeaderFail($manager,"createCampaignReport"))
        {
            return null;
        }

        return array(
                "count" => $results->getRowCount(),
                "ticketId" => $results->getReportTicketId(),
        );
    }

    public function getUniqueJobId ( $processState ) {
        if ( isset( $processState[ 'ticket' ][ 'ticketName' ] ) ) {
            return '::Ticket-' . $processState[ 'ticket' ][ 'ticketName' ];
        } else {
            return '';
        }
    }

    public function getTickets ( $espAccountId , $date ) {
        $campaigns = $this->getCampaigns( $espAccountId , $date );
        $tickets = [];

        $campaigns->each( function ( $campaign , $key ) use ( &$tickets , $espAccountId ) {
            $reportData = $this->createCampaignReport( $campaign->run_id );

            $tickets []= [
                "ticketName" => $reportData[ 'ticketId' ] ,
                "rowCount" => $reportData[ 'count' ] ,
                "campaignId" => $campaign->internal_id ,
                "espId" => $espAccountId
            ];
        } );

        return $tickets;
    }

    public function saveRecords ( &$processState ) {
        $this->dataRetrievalFailed = false;

        $recordData = $this->getCampaignReport(
            $processState[ 'ticket' ][ 'ticketName' ] ,
            $processState[ 'ticket' ][ 'rowCount' ]
        );

        if ( !is_null( $recordData ) ) {
            foreach ( $recordData as $key => $record ) {
                if ( $record[ 'action' ] === 'Open' ) {
                    $this->emailRecord->recordDeliverable(
                        self::RECORD_TYPE_OPENER ,
                        $record[ 'email' ] ,
                        $processState[ 'ticket' ][ 'espId' ] ,
                        $processState[ 'ticket' ][ 'campaignId' ] ,
                        $record[ 'actionDate' ]
                    );
                } elseif ( $record[ 'action' ] === 'Click' ) {
                    $this->emailRecord->recordDeliverable(
                        self::RECORD_TYPE_ClICKER ,
                        $record[ 'email' ] ,
                        $processState[ 'ticket' ][ 'espId' ] ,
                        $processState[ 'ticket' ][ 'campaignId' ] ,
                        $record[ 'actionDate' ]
                    );
                } elseif ( $record[ 'action' ] === 'Delivered' ) {
                    $this->emailRecord->recordDeliverable(
                        self::RECORD_TYPE_DELIVERABLE ,
                        $record[ 'email' ] ,
                        $processState[ 'ticket' ][ 'espId' ] ,
                        $processState[ 'ticket' ][ 'campaignId' ] ,
                        $record[ 'actionDate' ]
                    );
                }
            }
        } else {
            $this->processState[ 'delay' ] = 180;

            $this->dataRetrievalFailed = true;
        }
    }

    public function shouldRetry () { return $this->dataRetrievalFailed; }

    public function getCampaignReport($ticketId, $count){
        $manager = new ContactManagement();
        $offset = 0;
        $limit = 10;
        $totalCount = 100;
        $data = array();
        while ($totalCount > 0) {

            if ($limit > $totalCount) {
                $limit = $totalCount;
            }
            //Due to the dumb paging of campaigner
            $upToCount = $offset > 0 ? $offset + $limit -1 : $offset + $limit;
            $report = new DownloadReport($this->api->getAuth(),$ticketId, $offset, $upToCount, "rpt_Detailed_Contact_Results_by_Campaign");
            $manager->DownloadReport($report);
            if($this->checkforHeaderFail($manager,"getCampaignReport"))
            {
                return null;
            }
            $data = array_merge($data,$this->parseOutActions($manager));
            $offset = $upToCount;
            $totalCount = $totalCount - $limit;
        }
        return $data;
    }

    private function checkforHeaderFail($manager, $jobName){
        $header = $this->api->parseOutResultHeader($manager);

        if ($header['errorFlag'] != "false" ) {
            throw new \Exception("{$header['errorFlag']} - {$this->api->getApiName()}::{$this->api->getEspAccountId()} Failed {$jobName} because {$header['returnMessage']} - {$header['returnCode']}");
        } else if ($header['returnCode'] == self::NO_CAMPAIGNS) {
            Log::info("{$this->api->getApiName()}::{$this->api->getEspAccountId()} had no campaigns");
            return true;
        }
        return false;
    }

    private function parseOutActions($manager){
        $body = simplexml_load_string($manager->__getLastResponse());
        $response = $body->children("http://schemas.xmlsoap.org/soap/envelope/")->Body->children();
        $entries = $response->DownloadReportResponse->DownloadReportResult->ReportResult;
        $return = array();
        foreach($entries as $entry){

            $email = (string)$entry->attributes()->ContactUniqueIdentifier;
            $action = (string)$entry->Action->attributes()->Type;
            $actionDate = (string)$entry->Action;
            $return[] = array(
                "action" => $action,
                "actionDate" => $actionDate,
                "email" => $email,
            );
        }
        return $return;
    }
}
