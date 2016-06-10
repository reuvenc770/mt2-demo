<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 6/9/16
 * Time: 11:15 AM
 */

namespace App\Services\API;

use App\Exceptions\JobException;
use App\Library\Bronto\messageFilter;
use App\Library\Bronto\readMessages;
use App\Services\API\EspBaseAPI;
use App\Library\Bronto\Login;
use App\Library\Bronto\deliveryFilter;
use App\Library\Bronto\readDeliveries;
use App\Library\Bronto\readRecentInboundActivities;
use App\Library\Bronto\BrontoSoapApiImplService as BrontoSoapApi;

class BrontoApi extends EspBaseAPI
{
    CONST ESP_NAME = "Bronto";
    private $token;
    private $brontoObject;

    public function __construct($espAccountId)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);
        //$creds = EspApiAccount::grabApiKeyWithSecret($espAccountId);
        $this->token = "18CC1C1A-6F9A-40D9-81D2-3B2527C98A33";
        $this->brontoObject = new BrontoSoapApi();
        $sessionId = $this->brontoObject->login(new Login($this->token))->getReturn();
        $session_header = new \SoapHeader("http://api.bronto.com/v4",
            'sessionHeader',
            array('sessionId' => $sessionId));
        $this->brontoObject->__setSoapHeaders(array($session_header));

    }


    public function sendApiRequest()
    {
        // TODO: Implement sendApiRequest() method.
    }


    public function getCampaigns($filter)
    {
        $this->test();
        $deliveries = $this->brontoObject->readDeliveries(new readDeliveries($filter, 0, 0, 1, 0))->getReturn();
        $return = array();
        foreach ($deliveries as $delivery) {
            $filter = new messageFilter();
            $filter->id = $delivery->getMessageId();
            $message = $this->brontoObject->readMessages(new readMessages($filter, 0, 1, 10, 0))->getReturn()[0];
            $delivery->messageName = $message->getName();
            $return[] = $delivery;
        }
        return $return;

    }

    public function test()
    {
        $startDate = date('c', strtotime('-1 days'));

        // First Page
        $filter = array(
            "start" => $startDate,
            "size" => "5000",
            "types" => array("click"),
            "readDirection" => 'First',
        );
        $test = $this->brontoObject->readRecentInboundActivities(new readRecentInboundActivities($filter))->getReturn();
        print_r($test);

        $filter = array(
            "start" => $startDate,
            "size" => "5000",
            "types" => array("click"),
            "readDirection" => 'NEXT',
        );
        try {
            $test = $this->brontoObject->readRecentInboundActivities(new readRecentInboundActivities($filter));
        } catch (\SoapFault $e){
            if($e->getMessage() != "116: End of result set"){
                throw new JobException($e->getMessage());
            }
        }


    }
}