<?php

namespace App\Services;

use App\Services\API\CampaignerApi;
use Carbon\Carbon;
use App\Library\Campaigner\ContactManagement;
use App\Library\Campaigner\RunReport;

class CampaignerSubscriberService {
    protected $api;

    protected $request = null;
    protected $methodData = null;
    protected $endDate;

    public function __construct(CampaignerApi $api)
    {
        $this->api = $api;
        $this->endDate = Carbon::now()->endOfDay()->toDateString();
        $this->contactManagement = new ContactManagement();
    }

    public function pullBounceEmailsByLookback($lookback){
        throw new \Exception("DEAD MANS LAND NOT ENABLED");
        /**
        $start = Carbon::now()->startOfDay()->subDay($lookback)->toDateString();
        $this->request = self::HARDBOUNCE_REQUEST;
        $this->methodData = ["start_date"=> $start, "end_date" => $this->endDate ];
        $emails = $this->_handleRequest();
        print_r($emails);
         * **/

    }
    public function pullUnsubsEmailsByLookback($lookback){


        $date = Carbon::now()->subDay()->setTimezone('America/New_York');
        #$fileName = $sites[$siteName]['folder']."Unsubs_" . $date->format('Ydm') . ".csv";
        ​
        try {
            echo "Pulling unsubs for Campaigner ...";
            $report = $this->handleRequest();
            echo "...Finished\n";
            var_dump($report);
            return $report;

            // do we really need to download this?
            /*
            if (!empty($report)) {
                echo "Trying to download file for {$siteName}...";
                $this->api->downloadUnsubReport($fileName, $report);
                echo "...Finished\n";
            }
            */
        }
        catch (\Exception $e) {
            echo $e->getMessage();
        }

        // return SOMETHING here
        // previous was return $return->item->responseData->manifest->deleted_contact_data;

    }

    private function handleRequest(){
        // this should return xmlbody

        try {
            $this->api->buildRequest($this->request, $this->methodData);
            $response = $this->api->sendApiRequest();
            $xmlBody = simplexml_load_string($response->getBody()->__toString());
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        if ($xmlBody->item->responseCode != 201) {
            throw new \Exception($xmlBody->asXML());
        }
        return $xmlBody;
    }
}
