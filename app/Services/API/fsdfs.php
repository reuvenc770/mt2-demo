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
use App\Library\AWeber\OAuthUser;
use Cache;
class AWebesdfdsfsrApi extends EspBaseAPI
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
            //$accountId = Cache::remember('aweber_account_'.$espAccountId, $time, function() {
            //
            //});

            //$user = new OAuthUser();
            //$user->accessToken = $this->accessToken;
            // $user->tokenSecret = $this->sharedSecret;
            // $this->api->adapter->user = $user;
            // $listId = Cache::remember('aweber_list_id_'.$espAccountId, $time, function() use ($accountId) {
            //return $this->api->adapter->request('GET', "/accounts/{$accountId}/lists/", array())['entries'][0]['id'];
            // });

            //$this->url = "/accounts/{$accountId}/lists/{$listId}/";
        } catch (AWeberAPIException $exc) {
            //Log::error("AWeber  Failed {$exc->type} due to {$exc->message} help:: {$exc->documentation_url}");
            //throw new \Exception("AWeber  Failed {$exc->type} due to {$exc->message} help:: {$exc->documentation_url}");
        }
    }


    /**
     * handled by api
     */
    public function sendApiRequest()
    {


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
     * @param $link
     * @return mixed
     */
    private function getEmailAddress($link)
    {
        $response = $this->makeApiRequest($link, array(), true);
        return $response->email;
    }
}