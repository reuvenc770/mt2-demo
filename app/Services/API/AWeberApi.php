<?php

namespace App\Services\API;


/**
 * Class AWeber
 * @package App\Services\API
 */
use App\Facades\EspApiAccount;
use App\Library\AWeber\AWeberAPI as AWeberLibraryApi;
use App\Library\AWeber\AWeberAPIException;
use App\Library\AWeber\AWeberEntry;
use App\Library\AWeber\AWeberCollection;
use App\Library\AWeber\AWeberResourceNotImplemented;
use Log;
use App\Library\AWeber\OAuthUser;
use Cache;
class AWeberApi extends EspBaseAPI
{
    private $accessToken;
    private $sharedSecret;
    private $api;
    private $baseUrl;
    private $id; 
    private $account;
    const COUNTER = 0;
    const ESP_NAME = "AWeber";

    public function __construct($espAccountId)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);
        $creds = EspApiAccount::getKeysWithOAuth($espAccountId);
        $key = $creds['accessToken'];
        $secret = $creds['accessSecret'];
        $time = 60 * 4;
        $this->id = $espAccountId;
        $weber = new AWeberLibraryApi($key, $secret);
        $this->accessToken = $creds['consumerToken'];
        $this->sharedSecret = $creds['consumerSecret'];
        $weber->adapter->debug = config('misc.a_weber_debug');
        try {
            $this->api = $weber;
            $this->account = Cache::remember('aweber_account_'.$espAccountId, $time,
                function() {
                    return $this->api->getAccount($this->accessToken, $this->sharedSecret);
                });
            $this->baseUrl = "/accounts/{$this->account->id}/";
        } catch (AWeberAPIException $exc) {
            Log::error("AWeber  Failed {$exc->type} due to {$exc->message} help:: {$exc->documentation_url}");
            throw new \Exception("AWeber  Failed {$exc->type} due to {$exc->message} help:: {$exc->documentation_url}");
        }
    }

    public function sendApiRequest()
    {
        //not used
    }
    
    

    /**
     * @param int $limit
     * @return AWeberCollection|AWeberEntry
     */
    public function getCampaigns($lists, $limit = 20)
    {
        $campaignData = [];
        $numberToPull = $limit; //lets get the last X campaigns sent
        foreach($lists as $list){
            $i = 0;
            $campaigns = $this->makeApiRequest($list->campaigns_collection_link, array("ws.size" => 10),true);
            foreach($campaigns as $campaign){

                try{
                    $sentAt = $campaign->sent_at;
                } catch (AWeberResourceNotImplemented $e){
                    //Follow Up Email
                    continue;
                }

                $i++;
                echo "{$i} -- {$campaign->self_link}\n";
                $row = array(
                    "list_id" =>$list->id,
                    "internal_id" => $campaign->id,
                    "subject" => $campaign->subject,
                    "sent_at" => $sentAt,
                    "info_url" => $campaign->self_link,
                    "total_sent" => $campaign->total_sent,
                    "total_opens" => $campaign->total_opens,
                    "total_unsubscribes" => $campaign->total_unsubscribes,
                    "total_clicks" => $campaign->total_clicks,
                    "total_undelivered" => $campaign->total_undelivered,

                );
              $campaignData[] = $row;
                if($i == $numberToPull){
                    $i = 0;
                    break;
                }
            }
        }
       return $campaignData;
    }

    public function getAllUnsubs(){
            $params = array('status' => 'unsubscribed');
            return $found_subscribers = $this->account->findSubscribers($params);
    }

    /**
     * @param $campaignId
     * @param $type
     * @return mixed
     */
    public function getStateValue($listId, $campaignId, $type)
    {
        $url = "/lists/{$listId}/campaigns/b{$campaignId}/stats/{$type}";
        $response = $this->makeApiRequest($url);
        return $response->value;
    }

    public function getStateValueFromUrl($url)
    {
        $response = $this->makeApiRequest($url,array(),true);
        return $response->value;
    }

    /**
     * @param $incomingUrl
     * @param array $params
     * @param bool $fullUrl
     * @return AWeberCollection|AWeberEntry
     */
    public function makeApiRequest($incomingUrl, $params = array(), $fullUrl = false)
    {
        $user = new OAuthUser();
        $user->accessToken = $this->accessToken;
        $user->tokenSecret = $this->sharedSecret;
        $this->api->adapter->user = $user;
        $url = $this->baseUrl . $incomingUrl;
        if ($fullUrl) {
            $url = $incomingUrl;
        }
        
        $response = $this->api->adapter->request('GET', $url, $params);
        if (!empty($response['id'])) {
            return new AWeberEntry($response, $url, $this->api->adapter);
        } else if (array_key_exists('entries', $response)) {
            return new AWeberCollection($response, $url, $this->api->adapter);
        } else {
            return $response;
        }
    }

    //We return everything raw and do not return an object. 
    public function makeRawApiRequest($incomingUrl, $params = array(), $fullUrl = false)
    {
        $user = new OAuthUser();
        $user->accessToken = $this->accessToken;
        $user->tokenSecret = $this->sharedSecret;
        $this->api->adapter->user = $user;
        $url = $this->baseUrl . $incomingUrl;
        if ($fullUrl) {
            $url = $incomingUrl;
        }

        $response = $this->api->adapter->request('GET', $url, $params);
            return array (
                "response" => $response,
                "url"      => $url,
                "adapter"  => $this->api->adapter
            );
    }
    
}
