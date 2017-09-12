<?php
/**
 * User: rbertorelli
 */

namespace App\Services\API;

use App\Facades\EspApiAccount;
use App\Facades\Guzzle;
use Carbon\Carbon;

class MaroApi extends EspBaseAPI {

    const API_URL = "http://api.maropost.com/accounts/%d/reports.json?";
    const DELIVERED_URL = "http://api.maropost.com/accounts/%s/campaigns/%d/delivered_report.json?";
    const CAMPAIGN_OPEN_URL = "http://api.maropost.com/accounts/%s/campaigns/%d/open_report.json?";
    const CAMPAIGN_CLICK_URL = "http://api.maropost.com/accounts/%s/campaigns/%d/click_report.json?";
    const OPENS_URL = "http://api.maropost.com/accounts/%d/reports/opens.json?";
    const CLICKS_URL = "http://api.maropost.com/accounts/%d/reports/clicks.json?";
    const BOUNCES_URL = "http://api.maropost.com/accounts/%d/reports/bounces.json?";
    const COMPLAINTS_URL = "http://api.maropost.com/accounts/%d/reports/complaints.json?";
    const UNSUBS_URL = "http://api.maropost.com/accounts/%d/reports/unsubscribes.json?";
    const ADDL_INFO_URL = "http://api.maropost.com/accounts/%d/campaigns/";
    const ADD_CONTACT_URL = "http://api.maropost.com/accounts/%d/lists/%d/contacts.json?auth_token=";
    const CAMPAIGN_LIST_URL = "http://api.maropost.com/accounts/%d/campaigns.json";
    const ESP_NAME = "Maro";
    const RECORDS_PER_PAGE = 1000;
    const LOOKBACK_DAYS = 3;
    protected $apiKey;
    protected $priorDate;
    protected $date;
    protected $account;
    protected $deliverableStartDate;
    protected $deliverableEndDate;
    protected $espAccountId;

    public function __construct($espAccountId) {
        parent::__construct(self::ESP_NAME, $espAccountId);
        $creds = EspApiAccount::grabApiAccountIdAndKey($espAccountId);
        $this->account = $creds['account'];
        $this->apiKey = $creds['apiKey'];
        $this->espAccountId = $espAccountId;
    }

    public function getId () { return $this->espAccountId; }

    public function setDate($date) {
        $this->priorDate = $date;
        $this->date = Carbon::now()->toDateString();
    }

    public function sendApiRequest() {
        return Guzzle::get($this->url, ['verify' => false]);
    }

    public function constructApiUrl($page = null) {

        $baseUrl = sprintf(self::API_URL, $this->account);
        $baseUrl .= ('auth_token=' . $this->apiKey);
        
        if ($page) {
            $baseUrl .= '&page=' . $page;
        }
        if ($this->date) {
            $baseUrl .= '&from=' . $this->priorDate . '&to=' . $this->date;
        }
        $this->url = $baseUrl;
    }

    public function constructDeliverableUrl($type, $page = null) {

        switch ($type) {
            case 'opens':
                $this->url = sprintf(self::OPENS_URL, $this->account);
                break;

            case 'clicks':
                $this->url = sprintf(self::CLICKS_URL, $this->account);
                break;

            case 'bounces':
                $this->url = sprintf(self::BOUNCES_URL, $this->account);
                $this->url .= 'type=hard&';
                break;

            case 'complaints':
                $this->url = sprintf(self::COMPLAINTS_URL, $this->account);
                break;

            case 'unsubscribes':
                $this->url = sprintf(self::UNSUBS_URL, $this->account);
                break;

            default:
                throw new \Exception('Invalid action type');
                break;
        }

        // Add additional fields common to all calls
        $this->url .= 'fields="email"&auth_token=' 
                        . $this->apiKey
                        . '&per=' 
                        . self::RECORDS_PER_PAGE
                        . '&from='
                        . $this->deliverableStartDate
                        . '&to='
                        . $this->deliverableEndDate;

        if (!is_null($page)) {
            $this->url .= '&page=' . $page;
        }
    }

    public function setDeliverableLookBack($date = null) {
        if (null === $date) {
            $this->deliverableStartDate = Carbon::now()->subDay(self::LOOKBACK_DAYS)->toDateString();
        }
        else {
            $this->deliverableStartDate = Carbon::parse($date)->toDateString();
        }
        
        $this->deliverableEndDate = Carbon::now()->addDays(1)->toDateString();
    }

    public function constructAdditionalInfoUrl($campaignId) {
        $this->url = sprintf(self::ADDL_INFO_URL, $this->account) 
            . $campaignId 
            . '.json?auth_token='
            . $this->apiKey;
    }

    public function constructCampaignListUrl( $page = null ) {
        $this->url = sprintf(self::CAMPAIGN_LIST_URL, $this->account) 
            . '?auth_token='
            . $this->apiKey;

        if ($page) {
            $this->url .= '&page=' . $page;
        }
    }

    public function setActionUrl ( $campaignId, $actionType, $pageNumber = 0 ) {
        switch ($actionType) {
            case 'opens':
                $url = self::CAMPAIGN_OPEN_URL;
                break;
            case 'clicks':
                $url = self::CAMPAIGN_CLICK_URL;
                break;
            default:
                $url = self::DELIVERED_URL;
                break;
        }

        $this->url = sprintf( $url , $this->account , $campaignId ) 
                    . 'page=' . $pageNumber 
                    . '&auth_token=' . $this->apiKey;

    }

    public function addContact($record, $listId) {

        $url = sprintf(self::ADD_CONTACT_URL, $this->account, $listId);
        $url .= $this->apiKey;

        Guzzle::request('POST', $url, [
            'contents' => [
                'contact' => [
                    'first_name' => $record->first_name,
                    'last_name' => $record->last_name,
                    'email' => $record->email_address,
                    'subscribe' => true,
                    'remove_from_dnm' => true
                ]
            ]
        ]);
    }

    public function getAccountId(){
        return $this->account;
    }
}
