<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:31 PM
 */

namespace App\Services;

#use App\Services\API\BlueHornet;
use App\Repositories\ReportRepo;
use App\Services\API\BlueHornetApi;
use App\Services\AbstractReportService;
use League\Flysystem\Exception;
use Illuminate\Support\Facades\Event;
use App\Events\RawReportDataWasInserted;
use App\Services\Interfaces\IDataService;
use App\Services\EmailRecordService;
use Carbon\Carbon;
use Storage;

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
    protected $dataRetrievalFailed = false;

    /**
     * BlueHornetReportService constructor.
     * @param ReportRepo $reportRepo
     * @param $accountNumber
     */
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
            "end_date" => $endDate
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
            'deploy_id' => $report['message_name'],
            'sub_id' => $report['bill_codes'],
            'm_deploy_id' => 0, // stub for now
            'esp_account_id' => $report['esp_account_id'],
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
                    $jobId .= '::Campaign-' . $processState[ 'campaign' ][ 'internal_id' ];
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

    public function getTypeList () {
        return [ 'deliverable' , 'open' , 'click' , 'optout' ];
    }

    public function startTicket ( $espAccountId , $campaign , $recordType ) {
        return [
            "ticketName" => $this->getTicketForMessageSubscriberData( $campaign->internal_id , 'sent,bounce,open,click,optout' ) ,
            "campaignId" => $campaign->internal_id ,
            "espId" => $espAccountId
        ];
    }

    public function downloadTicketFile ( &$processState ) {
        try {
            $fileContents = $this->getFile( $processState[ 'ticketResponse' ] );
        } catch ( Exception $e ) {
            Log::error( 'Problems downloading file.' );

            return false;
        }

        $filePath = "files/deliverables/{$processState[ 'apiName' ]}/{$processState[ 'espAccountId' ]}/" . $processState[ 'campaign' ]->internal_id . "/" . Carbon::now( 'America/New_York' )->format( 'Y-m-d-H-i-s' ) . '.xml';

        Storage::put( $filePath , $fileContents );

        return $filePath;
    }

    public function saveRecords ( &$processState ) {
        $this->dataRetrievalFailed = false;

        $fileContents = Storage::get( $processState[ 'filePath' ] );

        $contactIterator = new SimpleXMLIterator( $fileContents );
        for ( $contactIterator->rewind() ; $contactIterator->valid() ; $contactIterator->next() ) {
            $currentContact = $contactIterator->current();
            $currentEmail = '';
            $contactSent = false;
            $contactBounced = false;
            $bounceDate = '';

            for ( $currentContact->rewind() ; $currentContact->valid() ; $currentContact->next() ) {

                if ( $currentContact->key() === 'sent' && $currentContact->current() == 1 ) {
                    $contactSent = true;
                }

                /**
                 * Bounce Check. If found, then this email was not deliverable
                 */
                if ( $currentContact->key() === 'bounce' ) {
                   $contactBounced = true;

                   $currentBounce = $currentContact->current();
                   $currentBounce->rewind();
                   $currentBounce->next();
                   $bounceDate = $currentBounce->current();
                } 

                if( $currentContact->key() === 'email' ) {
                    $currentEmail = $currentContact->current();
                }

                if ( $processState[ 'recordType' ] == 'optout' && $currentContact->key() === 'optout' ) {
                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_UNSUBSCRIBE ,
                            $currentEmail ,
                            $processState[ 'ticket' ][ 'espId' ] ,
                            $processState[ 'ticket' ][ 'campaignId' ] ,
                            $currentContact->current()
                        );
                }

                if( $processState[ 'recordType' ] == 'open' && $currentContact->key() === 'opens' ) {
                    $currentOpens = $currentContact->current();

                    for ( $currentOpens->rewind() ; $currentOpens->valid() ; $currentOpens->next() ) {
                        $currentOpenDate = $currentOpens->current();

                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_OPENER ,
                            $currentEmail ,
                            $processState[ 'ticket' ][ 'espId' ] ,
                            $processState[ 'ticket' ][ 'campaignId' ] ,
                            $currentOpenDate
                        );
                    }
                }

                if ( $processState[ 'recordType' ] == 'click' && $currentContact->key() === 'clicks' ) {
                    $currentClicks = $currentContact->current();

                    for ( $currentClicks->rewind() ; $currentClicks->valid() ; $currentClicks->next() ) {
                        $currentClick = $currentClicks->current();
                        $currentClick->rewind();
                        $currentClick->next();
                        
                        $currentClickDate = $currentClick->current();

                        $this->emailRecord->queueDeliverable(
                            self::RECORD_TYPE_CLICKER ,
                            $currentEmail , 
                            $processState[ 'ticket' ][ 'espId' ] ,
                            $processState[ 'ticket' ][ 'campaignId' ] ,
                            $currentClickDate
                        );
                    }
                }
            }

            if ( $processState[ 'recordType' ] == 'deliverable' && $contactSent && !$contactBounced ) {
                $this->emailRecord->queueDeliverable(
                    self::RECORD_TYPE_DELIVERABLE ,
                    $currentEmail , 
                    $processState[ 'ticket' ][ 'espId' ] ,
                    $processState[ 'ticket' ][ 'campaignId' ] ,
                    ''
                );
            }
        }

        $this->emailRecord->massRecordDeliverables();
    }

    public function cleanUp ( $processState ) {
        Storage::delete( $processState[ 'filePath' ] );
    }

    public function shouldRetry () { return $this->dataRetrievalFailed; }

    public function getTicketForMessageSubscriberData( $messageId , $recordType )
    {
        $methodData = array(
            "mess_id" => $messageId ,
            "action_type" => $recordType 
        );
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
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $status = (string) $xmlBody->item->responseData->task->item->status;
        if($status == "ERROR"){
            throw new \Exception("Task Status came back as Error");
        }
        if ($status == "COMPLETE"){
            $return  = (string) $xmlBody->item->responseData->task->item->task_response->file_name;
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
}
