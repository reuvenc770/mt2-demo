<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:31 PM
 */

namespace App\Services;
use App\Repositories\ReportsRepo;
use SimpleXMLElement;
use Guzzle;
//TODO strip out api call into own method
//TODO Create Save Record method
//TODO Create JOB
class BlueHornetService
{
    CONST API_URL = "https://echo.bluehornet.com/api/xmlrpc/index.php";
    protected $reportRepo;
    protected $apiKey;
    protected $sharedSecret;

    //WHen we build this for real the keys should be set via an array with constants
    // and set via a method in the job or wherever the call is being made
    public function __construct(ReportsRepo $reportRepo, $apiKey, $sharedSecret){

        $this->reportRepo = $reportRepo;
        $this->apiKey = $apiKey;
        $this->sharedSecret = $sharedSecret;

    }


    private function buildRequest($methodName,$dataPoints = null) {
        $xml = new SimpleXMLElement('<api></api>');
        $auth = $xml->addChild('authentication');
        $auth->addChild('api_key', $this->apiKey);
        $auth->addChild('shared_secret', $this->sharedSecret);
        $auth->addChild('response_type', "xml");
        $data = $xml->addChild('data');
        $methodCall = $data->addChild('methodCall');
        $methodCall->addChild('methodName', $methodName);
        if (!empty($dataPoints)) {
            foreach ($dataPoints as $name => $value) {
                    $methodCall->addChild($name, $value);
            }
        }
        return $xml->asXML();
    }

    public function retrieveReportStats($date)
    {
        $methodData = array(
            "date" => $date
        );
        $xml = $this->buildRequest('legacy.message_stats', $methodData);
        $response = $this->sendAPIRequest($xml);

        return $response->getBody()->__toString();
    }


    private function sendAPIRequest($data) {
       return Guzzle::request('POST',self::API_URL, [
            'form_params' => [
                'data' => $data,
            ]
        ]);
    }

}