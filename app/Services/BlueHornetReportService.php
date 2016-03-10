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
        $methodData = array(
            "date" => $date
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

    public function getTickets ( $espAccountId , $date ) {
        $campaigns = $this->getCampaigns( $espAccountId , $date );
        $tickets = [];

        Log::info( $campaigns );

        $campaigns->each( function ( $campaign , $key ) use ( &$tickets , $espAccountId ) {
            $tickets []= [
                "ticketName" => $this->getTicketForMessageSubscriberData( $campaign->internal_id ) ,
                "campaignId" => $campaign->internal_id ,
                "espId" => $espAccountId
            ];
        } );

        return $tickets; 
    }

    public function saveRecords ( &$processState ) {
        $this->dataRetrievalFailed = false;

        $ticket = $processState[ 'ticket' ][ 'ticketName' ];

        $fileName = $this->checkTicketStatus( $ticket );

        if ( $fileName !== false ) {
            $file = $this->getFile( $fileName );

            #Log::info( $file->asXML() );

            $contactIterator = new SimpleXMLIterator( $file->asXML() );
            for ( $contactIterator->rewind() ; $contactIterator->valid() ; $contactIterator->next() ) {
                $currentContact = $contactIterator->current();
                $currentEmail = '';
                $currentEmailId = 0;
                $contactSent = false;
                $contactBounced = false;
                $bounceDate = '';

                for ( $currentContact->rewind() ; $currentContact->valid() ; $currentContact->next() ) {

                    if ( $currentContact->key() === 'sent' && $currentContact->current() == 1 ) {
                        $contactSent = true;
                    }

                    if ( $currentContact->key() === 'bounce' ) {
                       $contactBounced = true;

                       $currentBounce = $currentContact->current();
                       $currentBounce->rewind();
                       $currentBounce->next();
                       $bounceDate = $currentBounce->current();
                    } 

                    if( $currentContact->key() === 'email' ) {
                        $currentEmail = $currentContact->current();
                        $currentEmailId = $this->emailRecord->getEmailId( $currentEmail );
                    }

                    if( $currentContact->key() === 'opens' ) {
                        $currentOpens = $currentContact->current();

                        for ( $currentOpens->rewind() ; $currentOpens->valid() ; $currentOpens->next() ) {
                            $currentOpenDate = $currentOpens->current();

                            $this->emailRecord->recordOpen(
                                $currentEmailId ,
                                $processState[ 'ticket' ][ 'espId' ] ,
                                $processState[ 'ticket' ][ 'campaignId' ] ,
                                $currentOpenDate
                            );
                        }
                    }

                    if ( $currentContact->key() === 'clicks' ) {
                        $currentClicks = $currentContact->current();

                        for ( $currentClicks->rewind() ; $currentClicks->valid() ; $currentClicks->next() ) {
                            $currentClick = $currentClicks->current();
                            $currentClick->rewind();
                            $currentClick->next();
                            
                            $currentClickDate = $currentClick->current();

                            $this->emailRecord->recordClick(
                                $currentEmailId , 
                                $processState[ 'ticket' ][ 'espId' ] , 
                                $processState[ 'ticket' ][ 'campaignId' ] ,
                                $currentClickDate
                            );
                             
                        }
                    }
                }

                if ( $contactSent && !$contactBounced ) {
                    $this->emailRecord->recordDeliverable(
                        $currentEmailId , 
                        $processState[ 'ticket' ][ 'espId' ] , 
                        $processState[ 'ticket' ][ 'campaignId' ] ,
                        $bounceDate
                    );
                }
            }
        } else {
            $this->dataRetrievalFailed = true;
        }
    }

    public function shouldRetry () { return $this->dataRetrievalFailed; }

    public function getTicketForMessageSubscriberData($messageId)
    {
        $methodData = array(
            "mess_id" => $messageId
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

    public function checkTicketStatus($ticketId){
        $return = false;
        $methodData = array(
            "task_id" => $ticketId
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
            $xmlBody = simplexml_load_string($response->getBody()->__toString());
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $xmlBody;
    }
}
