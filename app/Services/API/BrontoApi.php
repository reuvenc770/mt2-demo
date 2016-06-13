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
use App\Library\Bronto\readRecentInboundActivitiesResponse;
use App\Services\API\EspBaseAPI;
use App\Library\Bronto\Login;
use App\Library\Bronto\deliveryFilter;
use App\Facades\EspApiAccount;
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
        $token = EspApiAccount::grabAccessToken($espAccountId);
        $this->token = $token;

    }


    public function sendApiRequest()
    {
        // TODO: Implement sendApiRequest() method.
    }


    public function getCampaigns($filter)
    {
        $this->setupBronto();
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

    public function getDeliverablesByType($filter)
    {
        $this->setupBronto();
        $records = array();
        $firstSet = array();
        $secondSet = array();
        try {

            $firstSet = $this->brontoObject->readRecentInboundActivities(new readRecentInboundActivities($filter))->getReturn();
            while (1 != 2) {
                $filter['readDirection'] = "NEXT";
                $secondSet = $this->brontoObject->readRecentInboundActivities(new readRecentInboundActivities($filter))->getReturn();
                $records = array_merge($firstSet, $secondSet);
            }
        } catch (\SoapFault $e) {
            if ($e->getMessage() == "116: End of result set.") {
                //nothing to see here, besides a exception to get out of a while loop.
            } else {
                throw new JobException($e->getMessage());
            }
        } finally {
            $records = array_merge($firstSet, $secondSet);
        }
        return $records;
    }

    //PHP cannot serialize SoapCLient
    private function setupBronto()
    {
        $this->brontoObject = new BrontoSoapApi();
        $sessionId = $this->brontoObject->login(new Login($this->token))->getReturn();
        $session_header = new \SoapHeader("http://api.bronto.com/v4",
            'sessionHeader',
            array('sessionId' => $sessionId));
        $this->brontoObject->__setSoapHeaders(array($session_header));
    }

    public function getId()
    {
        return $this->espAccountId;
    }
}