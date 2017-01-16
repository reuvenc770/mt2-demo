<?php
namespace App\Services;


use App\Facades\Suppression;
use App\Models\AWeberSubscriber;
use App\Repositories\AWeberSubscriberRepo;
use App\Services\API\AWeberApi;


/**
 * Class AWeberReportService
 * @package App\Services
 */
class AWeberSubscriberService
{
    CONST INSERT_COUNT = 300;
    protected $api;
    protected $subscribers = [];
    protected $subscriberRepo;
    protected $count;


    public function __construct(AWeberApi $weberApi)
    {
        $this->api = $weberApi;
        $this->count = 0;
        //way to much code to refactor this in and once again very unique case.
        $this->subscriberRepo = new AWeberSubscriberRepo(new AWeberSubscriber());
    }

    public function pullUnsubsEmailsByLookback($lookback)
    {
            $records = $this->api->getAllUnsubs();
            return $records;
    }

    public function insertUnsubs($data, $espAccountId){
        foreach($data as $record){
            switch($record->unsubscribe_method){

                case "unsubscribe link":
                    Suppression::recordRawUnsub($espAccountId,$record->email,0, $record->unsubscribed_at);
                    break;
                case "customer cp":
                    Suppression::recordRawComplaint($espAccountId,$record->email,0, $record->unsubscribed_at);
                    break;
                case "undeliverable";
                    Suppression::recordRawHardBounce($espAccountId,$record->email,0, $record->unsubscribed_at);
                    break;
                default:
                    throw new \Exception("I am not even mad, but aweber created a new unsub method");
                    break;
            }

        }
    }

    public function getSubscribers($url){
        return $this->api->makeApiRequest($url,array("ws.size" => 100),true);
    }

    public function getSubscriber($url){
        return $this->api->makeApiRequest($url,array(),true);
    }

    public function queueSubscriber($subscriber){
        $this->subscribers[] =  "( "
            . join( " , " , [
                '"'.$subscriber->email.'"' ,
                $subscriber->id] )
            . " )";
        if (self::INSERT_COUNT <= sizeof($this->subscribers)) {
            $this->insertSubscribers();
        }
    }
    
    public function insertSubscribers(){
        $this->subscriberRepo->massUpsert($this->subscribers);
        $this->subscribers = [];
    }
    public function insertSubscriber($subscriber){
        $this->subscriberRepo->insertSubscriber($subscriber);
    }
    
    
}
