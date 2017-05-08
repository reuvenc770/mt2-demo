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
use App\Library\Bronto\readRecentOutboundActivities;
use App\Services\API\EspBaseAPI;
use App\Library\Bronto\login;
use App\Library\Bronto\deliveryFilter;
use App\Facades\EspApiAccount;
use App\Library\Bronto\readDeliveries;
use App\Library\Bronto\readRecentInboundActivities;
use App\Library\Bronto\BrontoSoapApiImplService as BrontoSoapApi;
use App\Library\Bronto\addContacts;


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

        $records = [];
        $pageNumber = 1;

        try {
            $deliveries = $this->brontoObject->readDeliveries(
                new readDeliveries(
                    $filter ,
                    false ,
                    false ,
                    $pageNumber ,
                    false
                )
            )->getReturn();

            do {

                foreach ( $deliveries as $currentDelivery ) {
                    $messageFilter = new messageFilter();
                    $messageFilter->id = $currentDelivery->getMessageId();

                    $messages = $this->brontoObject->readMessages(
                        new readMessages( $messageFilter , false , 1 , 10 , false )
                    )->getReturn();

                    if ( count( $messages ) ) {
                        $message = array_pop( $messages );

                        $currentDelivery->messageName = $message->getName();

                        $records[] = $currentDelivery;
                    }
                }

                $pageNumber++;

                $deliveries = $this->brontoObject->readDeliveries(
                    new readDeliveries(
                        $filter ,
                        false ,
                        false ,
                        $pageNumber ,
                        false
                    )
                )->getReturn();
            } while ( count( $deliveries ) > 0 );
        } catch ( \Exception $e ) {
            \Log::error( $e );
            throw $e;
        }

        return $records;
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
            if(!isset($firstSet)){
                return $records;
            }
            $records = array_merge($firstSet, $secondSet);
        }
        return $records;
    }

    public function getOutgoingSends($filter){
        $this->setupBronto();
        $records = array();
        $firstSet = array();
        $secondSet = array();
        try {

            $firstSet = $this->brontoObject->readRecentOutboundActivities(new readRecentOutboundActivities($filter))->getReturn();
            while (1 != 2) {
                $filter['readDirection'] = "NEXT";
                $secondSet = $this->brontoObject->readRecentOutboundActivities(new readRecentOutboundActivities($filter))->getReturn();
                $records = array_merge($firstSet, $secondSet);
            }
        } catch (\SoapFault $e) {
            if ($e->getMessage() == "116: End of result set.") {
                //nothing to see here, besides a exception to get out of a while loop.
            } else {
                throw new JobException($e->getMessage());
            }
        } finally {
            if(!isset($firstSet)){
                return $records;
            }
            $records = array_merge($firstSet, $secondSet);
        }
        return $records;
    }

    //PHP cannot serialize SoapCLient
    private function setupBronto()
    {
        $this->brontoObject = new BrontoSoapApi();
        $sessionId = $this->brontoObject->login(new login($this->token))->getReturn();
        $session_header = new \SoapHeader("http://api.bronto.com/v4",
            'sessionHeader',
            array('sessionId' => $sessionId));
        $this->brontoObject->__setSoapHeaders(array($session_header));
    }

    public function getId()
    {
        return $this->espAccountId;
    }

    public function addContact($contactInfo)
    {
        $this->setupBronto();
        $result = $this->brontoObject->addContacts(new addContacts($contactInfo));
    }

}
