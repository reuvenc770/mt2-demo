<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:31 PM
 */

namespace App\Services;


use App\Facades\DeployActionEntry;
use App\Facades\Suppression;
use App\Repositories\ReportRepo;
use App\Services\API\BlueHornetApi;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;
use Carbon\Carbon;
use Storage;
use App\Exceptions\JobException;

use Log;
use SimpleXML;
use SimpleXMLElement;
use SimpleXMLIterator;

//TODO FAILED MONITORING - better error messages
//TODO Create Save Record method
/**
 * Class BlueHornetReportService
 * @package App\Services
 */
class BlueHornetReportService extends AbstractReportService implements IDataService
{
    /**
     * BlueHornetReportService constructor.
     * @param ReportRepo $reportRepo
     * @param $accountNumber
     */

    const DELIVERABLE_LOOKBACK = 2;

    public function __construct(ReportRepo $reportRepo, BlueHornetApi $api , EmailRecordService $emailRecord )
    {
        parent::__construct($reportRepo, $api , $emailRecord );
    }

    /**
     * @param $date
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function retrieveApiStats($date)
    {
        $endDate = Carbon::now()->endOfDay()->toDateString();
        $methodData = array(
            "start_date" => $date,
            "end_date" => $endDate,
            "last"     => 1000
        );
        try {
            $this->api->buildRequest('legacy.message_stats', $methodData);
            $response = $this->api->sendApiRequest();
            $xmlBody = simplexml_load_string($response->getBody()->__toString());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        if ($xmlBody->item->responseCode != 201) {
            throw new \Exception($xmlBody->asXML());
        }
        return $xmlBody;
    }

    public function insertApiRawStats($xmlData)
    {
        $arrayReportList = array();
        $reports = $xmlData->item->responseData->message_data;
        $espAccountId = $this->api->getEspAccountId();

        foreach ($reports->message as $report) {
            $convertedReport = $this->mapToRawReport($report);
            $this->insertStats($espAccountId, $convertedReport);
            $arrayReportList[] = $convertedReport;
        }

        Event::fire(new RawReportDataWasInserted($this, $arrayReportList));
    }

    public function mapToStandardReport($report)
    {
        return array(
            'campaign_name' => $report['message_name'],
            'external_deploy_id' => $this->getDeployId($report['bill_codes']),
            'm_deploy_id' => $this->getDeployId($report['bill_codes']), // temporarily the same as external
            'esp_account_id' => $report['esp_account_id'],
            'esp_internal_id' => $report['internal_id'],
            'datetime' => $report['date_sent'],
            'name' => $report['message_name'],
            'subject' => $report['message_subject'],
            'from' => null,
            'from_email' => null,
            'e_sent' => $report['sent_total'],
            'delivered' => $report['delivered_total'],
            'bounced' => $report['bounced_total'],
            'optouts' => $report['optout_total'],
            'e_opens' => $report['opened_total'],
            'e_opens_unique' => $report['opened_unique'],
            'e_clicks' => $report['clicked_total'],
            'e_clicks_unique' => $report['clicked_unique']
        );

    }

    private function getDeployId($campaignName) {
        $arr = explode('_', $campaignName);
        if (count($arr) > 0 && '' !== $arr[0]) {
            return (int)$arr[0];
        }
        else {
            return 0;
        }
    }

    public function mapToRawReport($report)
    {
        return array(
            "internal_id" => (string)$report['id'],
            "esp_account_id" => $this->api->getEspAccountId(),
            "message_subject" => (string)$report->message_subject,
            "message_name" => (string)$report->message_name,
            "date_sent" => (string)$report->date_sent,
            "message_notes" => (string)$report->message_notes,
            "withheld_total" => (string)$report->withheld_total,
            "globally_suppressed" => (string)$report->globally_suppressed,
            "suppressed_total" => (string)$report->suppressed_total,
            "bill_codes" => (string)$report->bill_codes,
            "sent_total" => (string)$report->sent_total,
            "sent_total_html" => (string)$report->sent_total_html,
            "sent_total_plain" => (string)$report->sent_total_plain,
            "sent_rate_total" => (string)$report->sent_rate_total,
            "sent_rate_html" => (string)$report->sent_rate_html,
            "sent_rate_plain" => (string)$report->sent_rate_plain,
            "delivered_total" => (string)$report->delivered_total,
            "delivered_html" => (string)$report->delivered_html,
            "delivered_plain" => (string)$report->delivered_plain,
            "delivered_rate_total" => (string)$report->delivered_rate_total,
            "delivered_rate_html" => (string)$report->delivered_rate_html,
            "delivered_rate_plain" => (string)$report->delivered_rate_plain,
            "bounced_total" => (string)$report->bounced_total,
            "bounced_html" => (string)$report->bounced_html,
            "bounced_plain" => (string)$report->bounced_plain,
            "bounced_rate_total" => (string)$report->bounced_rate_total,
            "bounced_rate_html" => (string)$report->bounced_rate_html,
            "bounced_rate_plain" => (string)$report->bounced_rate_plain,
            "invalid_total" => (string)$report->invalid_total,
            "invalid_rate_total" => (string)$report->invalid_rate_total,
            "has_dynamic_content" => (string)$report->has_dynamic_content,
            "has_delivery_report" => (string)$report->has_delivery_report,
            "link_append_statement" => (string)$report->link_append_statement,
            "timezone" => (string)$report->timezone,
            "ftf_forwarded" => (string)$report->ftf_forwarded,
            "ftf_signups" => (string)$report->ftf_signups,
            "ftf_conversion_rate" => (string)$report->ftf_conversion_rate,
            "optout_total" => (string)$report->optout_total,
            "optout_rate_total" => (string)$report->optout_rate_total,
            "opened_total" => (string)$report->opened_total,
            "opened_unique" => (string)$report->opened_unique,
            "opened_rate_unique" => (string)$report->opened_rate_unique,
            "opened_rate_aps" => (string)$report->opened_rate_aps,
            "clicked_total" => (string)$report->clicked_total,
            "clicked_unique" => (string)$report->clicked_unique,
            "clicked_rate_unique" => (string)$report->clicked_rate_unique,
            "clicked_rate_aps" => (string)$report->clicked_rate_aps,
            "campaign_name" => (string)$report->campaign_name,
            "campaign_id" => (string)$report->campaign_id
        );
    }

    public function getUniqueJobId ( &$processState ) {
        $jobId = ( isset( $processState[ 'jobId' ] ) ? $processState[ 'jobId' ] : '' );

        if ( 
            !isset( $processState[ 'jobIdIndex' ] )
            || ( isset( $processState[ 'jobIdIndex' ] ) && $processState[ 'jobIdIndex' ] != $processState[ 'currentFilterIndex' ] )
        ) {
            switch ( $processState[ 'currentFilterIndex' ] ) {
                case 2 :
                    $jobId .= ( isset( $processState[ 'campaign' ] ) ? '::Campaign-' . $processState[ 'campaign' ]->esp_internal_id : '' );
                break;

                case 3 :
                    $jobId .= '::Ticket-' . $processState[ 'ticket' ][ 'ticketName' ];
                break;

                case 6 :
                    $jobId .= '::Types-' . join( ',' , $processState[ 'typeList' ] );
                break;
            }
            
            $processState[ 'jobIdIndex' ] = $processState[ 'currentFilterIndex' ];
            $processState[ 'jobId' ] = $jobId;
        }

        return $jobId;
    }

    public function getTypeList ( &$processState ) {
        if ('rerun' === $processState['pipe']) {
            $typeList = [];
            // data in $processState['campaign']

            if (1 == $processState['campaign']->delivers) {
                $typeList[] = "deliverable";
            }
            if (1 == $processState['campaign']->opens) {
                $typeList[] = 'open';
            }
            if (1 == $processState['campaign']->clicks) {
                $typeList[] = 'click';
            }
            if (1 == $processState['campaign']->unsubs) {
                $typeList[] = 'optout';
            }
            if (1 == $processState['campaign']->bounces) {
                $typeList[] = 'bounce';
            }

            return $typeList;
        }
        else {
            if (isset($processState['recordType']) && 'delivered' === $processState['recordType']) {
                return ['deliverable', 'optout', 'bounce'];
            }
            else {
                return ['open', 'click'];
            }
        }
    }

    public function startTicket ( $espAccountId , $campaign , $recordType, $isRerun = false ) {
        $actionTypes = $recordType === 'delivered' ? 'sent,bounce,optout' : 'open,click';

        try {
            return [
                "ticketName" => $this->getTicketForMessageSubscriberData( $campaign->esp_internal_id , $actionTypes, $isRerun ) ,
                "deployId" => $campaign->external_deploy_id,
                "espInternalId" => $campaign->esp_internal_id ,
                "deliveryTime" => $campaign->datetime,
                "espId" => $espAccountId
            ];
        } catch ( \Exception $e ) {
            $jobException = new JobException( 'Failed to start report ticket. ' . $e->getMessage() , JobException::NOTICE , $e );
            $jobException->setDelay( 180 );
            throw $jobException;
        }
    }

    public function downloadTicketFile ( &$processState ) {
        try {
            $fileContents = $this->getFile( $processState[ 'ticketResponse' ] );
        } catch ( \Exception $e ) {
            $jobException = new JobException( 'Failed to download report ticket. ' . $e->getMessage() , JobException::NOTICE , $e );
            $jobException->setDelay( 180 );
            throw $jobException;
        }

        $filePath = "files/deliverables/{$processState[ 'apiName' ]}/{$processState[ 'espAccountId' ]}/" . $processState[ 'campaign' ]->esp_internal_id . "/" . Carbon::now( 'America/New_York' )->format( 'Y-m-d-H-i-s' ) . '.xml';

        Storage::put( $filePath , $fileContents );
        return $filePath;
    }

    public function saveRecords ( &$processState ) {
        $count = 0;
        try {
            $fileContents = Storage::get( $processState[ 'filePath' ] );

            $recordXML = new \DOMDocument();
            $recordXML->loadXML( $fileContents );
            $xpath = new \DOMXpath( $recordXML );

            
            switch ( $processState[ 'recordType' ] ) {
                case 'deliverable' :
                   $count = $this->queueDeliveredRecords( $xpath , $processState );
                break;

                case 'bounce' :
                    $count = $this->queueBouncedRecords( $xpath , $processState );
                break;

                case 'open' :
                    $count = $this->queueOpenedRecords( $xpath , $processState );
                break;

                case 'click' :
                    $count = $this->queueClickedRecords( $xpath , $processState );
                break;

                case 'optout' :
                    $count = $this->queueOptedOutRecords( $xpath , $processState );
                break;
            }

            unset( $recordXML );
            unset( $xpath );

           $this->emailRecord->massRecordDeliverables();
        } catch ( \Exception $e ) {
            $exceptionType  = get_class($e);
            DeployActionEntry::recordFailedRun($this->api->getEspAccountId(), $processState[ 'campaign' ]->esp_internal_id,$processState[ 'recordType' ] );
            $jobException = new JobException( "Failed to process report file - $exceptionType: " . $e->getMessage() , JobException::WARNING , $e );
            $jobException->setDelay( 60 );
            throw $jobException;
        }
        DeployActionEntry::recordSuccessRun($this->api->getEspAccountId(), $processState[ 'campaign' ]->esp_internal_id,$processState[ 'recordType' ] );
        return $count;
    }

    protected function queueDeliveredRecords ( $xpath , $processState ) {
        $contacts = $xpath->query( '//contact' );

        $count = 0;
        foreach ( $contacts as $current ) {
            $contents = $current->childNodes;

            $email = null;
            $isSent = false;
            $isBounce = false;

            foreach ( $contents as $detail ) {
                if ( $detail->nodeName == 'email' ) {
                    $email = $detail->nodeValue;
                } elseif ( $detail->nodeName == 'sent' && $detail->nodeValue == 1 ) {
                    $isSent = true;
                } elseif ( $detail->nodeName == 'bounce' ) {
                    $isBounce = true;
                }
            }
            
            if ( $isSent && !$isBounce ) {
                $time = $processState['ticket']['deliveryTime'] === '0000-00-00 00:00:00' ? null : $processState['ticket']['deliveryTime'];

                $this->emailRecord->queueDeliverable(
                    self::RECORD_TYPE_DELIVERABLE ,
                    $email , 
                    $processState[ 'ticket' ][ 'espId' ] ,
                    $processState[ 'ticket' ][ 'deployId' ] ,
                    $processState[ 'campaign' ]->esp_internal_id ,
                    $time
                );
                $count++;
            }
        }
        return $count;
    }

    protected function queueBouncedRecords ( $xpath , $processState ) {
        $count = 0;
        $bounces = $xpath->query( '*/bounce' );

        foreach ( $bounces as $current ) {
            $contents = $current->childNodes;
            $email = $this->findEmail( $current );
            $reason = null;
            $date = null;

            if ( is_null( $email ) ) { continue; }

            foreach ( $contents as $detail ) {
                if ( $detail->nodeName == 'reason' ) {
                    $reason = $detail->nodeValue;
                } elseif ( $detail->nodeName == 'date' ) {
                    $date = $detail->nodeValue;
                }
            }
            if ($reason == "5-Timeout" || $reason == "5-Spam Block" || "5-Mail Block" === $reason){
                continue;
            }
            Suppression::recordRawHardBounce(
                $processState[ 'ticket' ][ 'espId' ] ,
                $email , 
                $processState[ 'campaign' ]->esp_internal_id , 
                $date
            );
            $count++;
        }
         return $count;
    }

    protected function queueOpenedRecords ( $xpath , $processState ) {
        $count = 0;
        $opens = $xpath->query( '*/opens' );

        foreach ( $opens as $current ) {
            $contents = $current->childNodes;
            $email = $this->findEmail( $current );

            if ( is_null( $email ) ) { continue; }

            foreach ( $contents as $date ) {
                if ( trim( $date->nodeValue ) !== '' ) {
                    $this->emailRecord->queueDeliverable(
                        self::RECORD_TYPE_OPENER ,
                        $email ,
                        $processState[ 'ticket' ][ 'espId' ] ,
                        $processState[ 'ticket' ][ 'deployId' ] ,
                        $processState[ 'campaign' ]->esp_internal_id ,
                        $date->nodeValue
                    );
                    $count++;
                }
            }
        }
        return $count;
    }

    protected function queueClickedRecords ( $xpath , $processState ) {
        $count = 0;
        $clicks = $xpath->query( '///click' );

        foreach ( $clicks as $current ) {
            $contents = $current->childNodes;
            $email = $this->findEmail( $current , true );

            foreach ( $contents as $detail ) {
                if ( $detail->nodeName == 'date' ) {                    
                    $this->emailRecord->queueDeliverable(
                        self::RECORD_TYPE_CLICKER ,
                        $email , 
                        $processState[ 'ticket' ][ 'espId' ] ,
                        $processState[ 'ticket' ][ 'deployId' ] ,
                        $processState[ 'campaign' ]->esp_internal_id ,
                        $detail->nodeValue
                    );
                    $count++;
                }
            }
        }
        return $count;
    }

    protected function queueOptedOutRecords ( $xpath , $processState ) {
        $count = 0;
        $optouts = $xpath->query( '*/optout' );
        foreach ( $optouts as $current ) {
            $optoutDate = $current->nodeValue;

            $email = $this->findEmail( $current );
            $date = null;

            if ( is_null( $email ) ) { continue; }

            Suppression::recordRawUnsub(
                $processState[ 'ticket' ][ 'espId' ] ,
                $email ,
                $processState[ 'campaign' ]->esp_internal_id ,
                $optoutDate
            );
            $count++;
        }
        return $count;
    }

    protected function findEmail ( $currentNode , $isParentNode = false ) {
        if ( $isParentNode ) {
            $parent = $currentNode->parentNode;

            return $this->findEmail( $parent );
        } else {
            $sibling = $currentNode->previousSibling;

            if ( is_null( $sibling ) ) { return null; }

            if ( $sibling->nodeName == 'email' ) {
                return $sibling->nodeValue;
            }

            return $this->findEmail( $sibling );
        }
    }

    public function cleanUp ( $processState ) {
        Storage::delete( $processState[ 'filePath' ] );
    }

    public function getTicketForMessageSubscriberData( $messageId , $recordType, $isRerun )
    {

        $methodData = array(
            "mess_id" => $messageId ,
            "action_type" => $recordType
        );

        if (!$isRerun) {
            $methodData['start_date'] = Carbon::today()->subDay(self::DELIVERABLE_LOOKBACK)->toDateTimeString();
            $methodData['end_date'] = Carbon::today()->toDateTimeString();
        }

        try {
            $this->api->buildRequest("statistics.getMessageSubscriberData", $methodData);
            $response = $this->api->sendApiRequest();
            $xmlBody = simplexml_load_string($response->getBody()->__toString());

        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        if ($xmlBody->item->responseCode != 101) {
            throw new \Exception($xmlBody->asXML());
        }
        $ticketNumber = $xmlBody->item->responseData->task_id;
        if(is_null($ticketNumber)){
            throw new \Exception("Ticket Number Null");
        }
        return (string)$ticketNumber;
    }

    public function checkTicketStatus( &$processState ){
        $return = false;
        $methodData = array(
            "task_id" => $processState[ 'ticket' ][ 'ticketName' ]
        );

        try {
            $this->api->buildRequest("utilities.getTasks", $methodData);
            $response = $this->api->sendApiRequest();
            $xmlBody = simplexml_load_string($response->getBody()->__toString());

            $status = (string) $xmlBody->item->responseData->task->item->status;

            if ( $status == "ERROR"){
                throw new \Exception( "Ticket status is ERROR" );
            }

            if ( $status == "COMPLETE"){
                $return  = (string) $xmlBody->item->responseData->task->item->task_response->file_name;
            }

            if ( $return === false ) throw new \Exception( 'Ticket not ready.' );
        } catch ( \Exception $e ) {
            $jobException = new JobException( 'Failed to get report ticket status. ' . $e->getMessage() , JobException::NOTICE , $e );
            $jobException->setDelay( 180 );
            throw $jobException;
        }

        return $return;
    }

    public function getFile($filePath){
        $methodData = array(
            "file" => $filePath
        );
        try {
            $this->api->buildRequest("utilities.getFile", $methodData);
            $response = $this->api->sendApiRequest();
            $xmlBody = $response->getBody()->__toString();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $xmlBody;
    }

    public function addContactToLists(array $contactInfo) {

        foreach ($lists as $listId) {
            try {
                /* 
                For future reference, this is the BH format. Set this in the IPostingStrategy
                [
                    'email' => $record->email_address,
                    'external_id' => $record->email_id,
                    'template_id' => $listId,
                    'name' => ($record->first_name . ' ' . $record->last_name)
                ]
                */

                $this->api->buildRequest('transactional.sendtransaction', $contactInfo);
                $this->api->sendApiRequest();
            }
            catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }
    
}
