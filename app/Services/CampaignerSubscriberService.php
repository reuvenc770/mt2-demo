<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 5/10/16
 * Time: 11:54 AM
 */

namespace App\Services;


use App\Services\API\CampaignerApi;
use App\Exceptions\JobException;
use App\Library\Campaigner\ContactManagement;
use App\Library\Campaigner\DownloadReport;
use App\Library\Campaigner\RunReport;
use App\Jobs\DownloadUnsubTicket;
use App\Facades\Suppression;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\DispatchesJobs;

class CampaignerSubscriberService
{
    protected $api;
    use DispatchesJobs;
    public function __construct(CampaignerApi $api)
    {
        $this->api = $api;
    }

    public function pullUnsubsEmailsByLookback($lookback){
        try {
            $unsubReportData = $this->createUnsubReport($lookback);
            $bounceReportData = $this->createUnsubReport( $lookback , true );
        } catch ( \Exception $e ) {
            throw new JobException( 'Failed to start report ticket. ' . $e->getMessage() , JobException::NOTICE , $e );
        }

        if($unsubReportData){
            $this->dispatch(new DownloadUnsubTicket($this->api->getApiName(), $this->api->getEspAccountId(), $unsubReportData, str_random(16)));
        }

        if($bounceReportData){
            $this->dispatch(new DownloadUnsubTicket($this->api->getApiName(), $this->api->getEspAccountId(), $bounceReportData, str_random(16) , true ));
        }

        return null;

    }

    public function insertUnsubs($data){
        foreach ($data as $entry){
            Suppression::recordRawUnsub($this->api->getEspAccountId(),$entry['email'],0, Carbon::today()->toDateString());
        }

    }

    public function insertHardbounce ( $data ) {
        foreach ($data as $entry){
            Suppression::recordRawHardBounce(
                $this->api->getEspAccountId() ,
                $entry['email'] ,
                0 ,
                Carbon::today()->toDateString()
            );
        }
    }

    private function createUnsubReport( $lookback , $getHardbounces = null )
    {
        $manager = new ContactManagement();

        if ( $getHardbounces === true ) {
            $searchQuery = $this->api->buildHardbounceSearchQuery();
        } else {
            $searchQuery = $this->api->buildUnsubSearchQuery($lookback);
        }

        $report = new RunReport($this->api->getAuth(), $searchQuery);

        $reportHandle = $manager->RunReport($report);

        if ( !!is_a( $reportHandle , 'RunReportResponse' ) || !method_exists( $reportHandle , 'getRunReportResult' ) ) {
            throw new \Exception( 'Failed to create report.' );
        }

        $results = $reportHandle->getRunReportResult();

        if($this->api->checkforHeaderFail($manager,"createCampaignReport"))
        {
            return null;
        }

        return array(
            "count" => $results->getRowCount(),
            "ticketId" => $results->getReportTicketId(),
        );
    }

    public function getUnsubReport($ticketId, $count){
        $manager = new ContactManagement();
        $offset = 0;
        $limit = 20000;
        $totalCount = $count;
        $data = array();
        while ($totalCount > 0) {

            if ($limit > $totalCount) {
                $limit = $totalCount;
            }
            //Due to the dumb paging of campaigner
            $upToCount = $offset > 0 ? $offset + $limit -1 : $offset + $limit;
            $report = new DownloadReport($this->api->getAuth(),$ticketId, $offset, $upToCount, "rpt_Contact_Details");
            $manager->DownloadReport($report);
            if($this->api->checkforHeaderFail($manager,"getUnsubReport"))
            {
                return null;
            }
            $data = array_merge($data,$this->parseOutEmails($manager));
            $offset = $upToCount;
            $totalCount = $totalCount - $limit;
        }
        return $data;
    }

    private function parseOutEmails($manager){
        $lastResponse = $manager->__getLastResponse();
        $body = simplexml_load_string($lastResponse);

        if ( $body === false || !$body->asXml() ) {
            $errors = libxml_get_errors();
            echo "Errors:" . PHP_EOL;
            var_dump($errors);
            throw new \Exception( 'Failed to retrieve SOAP response.' );
        }

        $response = $body->children("http://schemas.xmlsoap.org/soap/envelope/")->Body->children();
        $entries = $response->DownloadReportResponse->DownloadReportResult->ReportResult;
        $return = array();
        foreach($entries as $entry){

            $email = (string)$entry->attributes()->ContactUniqueIdentifier;
            $return[] = array(
                "email" => $email,
            );
        }
        return $return;
    }
}
