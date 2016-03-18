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
use App\Services\EmailRecordService;
use Log;

class AWeberReportService extends AbstractReportService implements IDataService
{
    protected $dataRetrievalFailed = false;

    public function __construct(ReportRepo $reportRepo, AWeberApi $api , EmailRecordService $emailRecord )
    {
        parent::__construct($reportRepo, $api , $emailRecord );
    }

    //we may have to use date to hold offset, and build something that queries per page...
    public function retrieveApiStats($date)
    {
        $startTime = microtime( true );

        Log::info( 'Retrieving API Campaign Stats.......' );

        $date = null; //unfortunately date does not matter here.
        $campaignData = array();
        $campaigns = $this->api->getCampaigns(20);
        $i = 0;
          foreach ($campaigns as $campaign) {
                Log::info( 'Processing Aweber Campaign ' . $campaign->id );

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
              $i++;
              if($i >= 19){
                  break;
              }
          }

        $endTime = microtime( true );

        Log::info( 'Executed in: ' );
        Log::info(  $endTime - $startTime );

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

    public function getCampaigns ( $espAccountId , $date ) {
        return $this->reportRepo->getCampaigns( $espAccountId , $date )->splice( 0 , 20 );
    }

    public function getUniqueJobId ( $processState ) {
        if ( isset( $processState[ 'camapignId' ] ) && !isset( $processState[ 'recordType' ] ) ) {
            return '::Campaign' . $processState[ 'campaignId' ];
        } elseif ( isset( $processState[ 'camapignId' ] ) && isset( $processState[ 'recordType' ] ) ) {
            return '::Campaign' . $processState[ 'campaignId' ] . '::' . $processState[ 'recordType' ];
        } else {
            return '';
        }
    }

    public function splitTypes () {
        return [ 'opens' , 'clicks' ];
    }

    public function saveRecords ( &$processState ) {
        $this->dataRetrievalFailed = false;

        switch ( $processState[ 'recordType' ] ) {
            case 'opens' :
                try {
                    $opens = $this->api->getOpenReport( $processState[ 'campaignId' ] );
                } catch ( \Exception $e ) {
                    Log::error( 'Failed to retrieve open report. ' . $e->getMessage() );

                    $this->processState[ 'delay' ] = 180;

                    $this->dataRetrievalFailed = true;

                    return;
                }

                foreach ( $opens as $key => $openRecord ) {
                    $currentEmail = $openRecord[ 'email' ];
                    $currrentEmailId = $this->emailRecord->getEmailId( $currentEmail );

                    $this->emailRecord->recordOpen(
                        $currrentEmailId ,
                        $processState[ 'espId' ] ,
                        $processState[ 'campaignId' ] ,
                        $openRecord[ 'actionDate' ]
                    );
                }
            break;

            case 'clicks' :
                try {
                    $clicks = $this->api->getClickReport( $processState[ 'campaignId' ] );
                } catch ( \Exception $e ) {
                    Log::error( 'Failed to retrieve click report. ' . $e->getMessage() );

                    $this->processState[ 'delay' ] = 180;

                    $this->dataRetrievalFailed = true;

                    return;
                }

                foreach ( $clicks as $key => $clickRecord ) {
                    $currentEmail = $clickRecord[ 'email' ];
                    $currentEmailId = $this->emailRecord->getEmailId( $currentEmail );

                    $this->emailRecord->recordClick(
                        $currentEmailId ,
                        $processState[ 'espId' ] ,
                        $processState[ 'campaignId' ] ,
                        $clickRecord[ 'actionDate' ]
                    );
                }
            break;
        }
    }

    public function shouldRetry () { return $this->dataRetrievalFailed; }
}
