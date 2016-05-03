<?php

namespace App\Services;

use App\Services\API\CampaignerApi;
use Carbon\Carbon;

class CampaignerSubscriberService {
    protected $api;

    protected $request = null;
    protected $methodData = null;
    protected $endDate;

    public function __construct(CampaignerApi $api)
    {
        $this->api = $api;
        $this->endDate = Carbon::now()->endOfDay()->toDateString();
        
    }

    public function pullUnsubsEmailsByLookback($daysBack) {
        try {
            echo "Pulling unsubs for Campaigner ...";
            $report = $this->handleRequest($daysBack);
            echo "...Finished\n";
            var_dump($report);
            #return $report;
        }
        catch (\Exception $e) {
            echo "EXCEPTION CAUGHT: " . $e->getMessage();
        }
    }

    private function handleRequest($daysBack){

        try {
            $ticket = $this->api->startUnsubReport($daysBack);
            $response = $this->api->downloadUnsubs($ticket);
            return $this->processXml($response);
            
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if ($xmlBody->item->responseCode != 201) {
            throw new \Exception($xmlBody->asXML());
        }
        return $xmlBody;
    }

    private function processXml($xml) {
        $xmlBody = simplexml_load_string($xml);

        if (false === $xmlBody || !$xmlBody->asXml()) {
            echo "Campaigner XML Parsing Errors: " . PHP_EOL;
            var_dump(libxml_get_errors());
            throw new \Exception('Campaigner Unsub XML Parsing Errors');
        }
        else {
            $children = $xmlBody->children("http://schemas.xmlsoap.org/soap/envelope/");
            $response = $children->Body->children();
            return $response->DownloadReportResponse->DownloadReportResult->ReportResult;
        }
    }

    public function mapToSuppressionTable($entry, $espAccountId) {
        $typeText = $entry->attributes()->Status;

        // datetime set in UTC, going to switch to ET date
        $datetime = $entry->attributes()['DateCreatedUTC'];
        
        $date = new Carbon($datetime, 'UTC');
        $date->setTimezone('America/New_York');
        $day = $date->format('Y-m-d');
        echo $day . PHP_EOL;

        return [
            'email_address' => (string)$entry->attributes()['Email'],
            'reason' => $entry->attributes()['BounceCode'],
            'type_id' =>  $typeId = $typeText === 'unsubscribed' ? 1 : 2,
            'date' => $day,
            'campaign_id' => 0, // can't get campaigner's internal id
            'esp_account_id' => $espAccountId
        ];
    }

}
