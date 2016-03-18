<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/10/16
 * Time: 9:53 AM
 */

namespace App\Services\API;

use App\Facades\EspApiAccount;
use App\Library\AWeber\AWeberAPI as AWeberLibraryApi;
use App\Library\AWeber\AWeberAPIException;
use Log;
use App\Library\AWeber\AWeberEntry;
use App\Library\AWeber\AWeberCollection;
use Cache;
class AWeberApi extends EspBaseAPI
{
    private $accessToken;
    private $sharedSecret;
    private $api;
    private $url;

    public function __construct($name, $espAccountId)
    {
        parent::__construct($name, $espAccountId);
        $creds = EspApiAccount::grabAccessTokenAndSecret($espAccountId);
        $key = env("AWEBER_KEY", "");
        $secret = env("AWEBER_SECRET", "");
        $time = 60 * 4;
        $weber = new AWeberLibraryApi($key, $secret);
        $this->accessToken = $creds['accessToken'];
        $this->sharedSecret = $creds['accessSecret'];
        $weber->adapter->debug = false; //actually ok debugging
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


    /**
     * handled by api
     */
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
     * @param $campaignId
     * @return array
     */
    public function getClickReport($campaignId)
    {
        $url = "campaigns/b{$campaignId}/links/";
        $links = $this->makeApiRequest($url);
        $return = array();
        foreach ($links as $link) {
            if ($link->total_clicks > 1) {

                $linkUrl = $url . "{$link->id}/clicks";
                $clicks = $this->makeApiRequest($linkUrl);
                foreach ($clicks as $click) {
                    $linkClick = array(
                        "action" => $click->type,
                        "actionDate" => $click->event_time,
                        "email" => $this->getEmailAddress($click->subscriber_link),
                    );
                    sleep(1);//hate you so much
                }
            }
            $return[] = $linkClick;
        }
        return $return;
    }

    /**
     * @param $campaignId
     * @return array
     */
    public function getOpenReport($campaignId)
    {
        $url = "campaigns/b{$campaignId}/messages/";
        $messages = $this->makeApiRequest($url);
        foreach ($messages as $message) {
            if ($message->total_opens >= 1) {
                $opens = $this->makeApiRequest($message->opens_collection_link, array(), true);
                foreach ($opens as $open) {
                    $emailOpen = array(
                        "action" => $open->type,
                        "actionDate" => $open->event_time,
                        "email" => $this->getEmailAddress($open->subscriber_link),
                    );

                    $return[] = $emailOpen;
                }
            }
        }
        return $return;
    }

    /**
     * @param $incomingUrl
     * @param array $params
     * @param bool $fullUrl
     * @return AWeberCollection|AWeberEntry
     */
    private function makeApiRequest($incomingUrl, $params = array(), $fullUrl = false)
    {
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

    /**
     * @param $link
     * @return mixed
     */
    private function getEmailAddress($link)
    {
        $response = $this->makeApiRequest($link, array(), true);
        return $response->email;
    }
}
