<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/10/16
 * Time: 9:53 AM
 */

namespace App\Services\API;
use App\Facades\EspApiAccount;
use AWeberAPI as AWeberLibraryApi;
use AWeberAPIException;
use Log;
use DB;
use AWeberEntry;
use AWeberCollection;
class AWeberApi extends EspBaseAPI
{
    private  $accessToken;
    private  $sharedSecret;
    private  $api;
    private  $url;
    public function __construct($name, $espAccountId)
    {
        parent::__construct($name, $espAccountId);
        $creds = EspApiAccount::grabAccessTokenAndSecret($espAccountId);
        $key = env("AWEBER_KEY","");
        $secret = env("AWEBER_SECRET","");
        $weber = new AWeberLibraryApi($key,$secret);
        $this->accessToken = $creds['accessToken'];
        $this->sharedSecret = $creds['accessSecret'];
        $weber->adapter->debug = true;
        try {
            $this->api = $weber;
            $accountId = $this->api->getAccount($this->accessToken, $this->sharedSecret)->id;
            $listId = $this->api->adapter->request('GET',"/accounts/{$accountId}/lists/", array())['entries'][0]['id'];
            $this->url = "/accounts/{$accountId}/lists/{$listId}/";
        } catch(AWeberAPIException $exc){
            Log::error("AWeber  Failed {$exc->type} due to {$exc->message} help:: {$exc->documentation_url}");
            throw new \Exception("AWeber  Failed {$exc->type} due to {$exc->message} help:: {$exc->documentation_url}");
        }
    }
    //handled by api
    public function sendApiRequest() {


    }


    public function getCampaigns($limit = 100){
        $url = "campaigns";
        return $this->makeApiRequest($url, array("ws.size" => $limit));

    }

    public function getStateValue($campaignId, $type){
        $url = "campaigns/b{$campaignId}/stats/{$type}";
        $response = $this->makeApiRequest($url);
        return $response->value;
    }

    private function makeApiRequest($url, $params = array()){
        $url = $this->url.$url;
        $response = $this->api->adapter->request('GET',$url, $params);
        if (!empty($response['id'])) {
            return new AWeberEntry($response, $url, $this->api->adapter);
        } else if (array_key_exists('entries', $response)) {
            return new AWeberCollection($response, $url, $this->api->adapter);
        } else {
            return $response;
        }

    }
}