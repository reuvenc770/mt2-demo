<?php

namespace App\Services\API;


/**
 * Class AWeber
 * @package App\Services\API
 */
use App\Facades\EspApiAccount;
use App\Library\AWeber\AWeberAPI as AWeberLibraryApi;
use App\Library\AWeber\AWeberAPIException;
use Illuminate\Support\Facades\Cache;
use App\Library\AWeber\AWeberEntry;
use App\Library\AWeber\AWeberCollection;
use Log;
use App\Library\AWeber\OAuthUser;
class AWeberApi extends EspBaseAPI
{
    private $accessToken;
    private $sharedSecret;
    private $api;
    private $url;
    const COUNTER = 0;
    const ESP_NAME = "AWeber";

    public function __construct($espAccountId)
    {
        parent::__construct(self::ESP_NAME, $espAccountId);
        $creds = EspApiAccount::grabAccessTokenAndSecret($espAccountId);
        $key = env("AWEBER_KEY", "");
        $secret = env("AWEBER_SECRET", "");
        $time = 60 * 4;
        $weber = new AWeberLibraryApi($key, $secret);
        $this->accessToken = $creds['accessToken'];
        $this->sharedSecret = $creds['accessSecret'];
        $weber->adapter->debug = env("AWEBER_DEBUG", false);
        try {
            $this->api = $weber;
            $accountId = Cache::remember('aweber_account_'.$espAccountId, $time, function() {
            return $this->api->getAccount($this->accessToken, $this->sharedSecret)->id;
            });
            $listId = Cache::remember('aweber_list_id_'.$espAccountId, $time, function() use ($accountId) {
                return $this->api->adapter->request('GET', "/accounts/{$accountId}/lists/", array())['entries'][0]['id'];
            });


            $this->url = "/accounts/{$accountId}/lists/{$listId}/";

        } catch (AWeberAPIException $exc) {
            Log::error("AWeber  Failed {$exc->type} due to {$exc->message} help:: {$exc->documentation_url}");
            throw new \Exception("AWeber  Failed {$exc->type} due to {$exc->message} help:: {$exc->documentation_url}");
        }
    }

    public function sendApiRequest()
    {

    }


    /**
     * @param int $limit
     * @return AWeberCollection|AWeberEntry
     */
    public function getCampaigns($limit = 100)
    {
        $url = "campaigns";
        return $this->makeApiRequest($url, array("ws.size" => $limit));

    }

    /**
     * @param $campaignId
     * @param $type
     * @return mixed
     */
    public function getStateValue($campaignId, $type)
    {
        $url = "campaigns/b{$campaignId}/stats/{$type}";
        $response = $this->makeApiRequest($url);
        return $response->value;
    }

    /**
     * @param $incomingUrl
     * @param array $params
     * @param bool $fullUrl
     * @return AWeberCollection|AWeberEntry
     */
    private function makeApiRequest($incomingUrl, $params = array(), $fullUrl = false)
    {
        $user = new OAuthUser();
        $user->accessToken = $this->accessToken;
        $user->tokenSecret = $this->sharedSecret;
        $this->api->adapter->user = $user;
        $url = $this->url . $incomingUrl;
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

}
