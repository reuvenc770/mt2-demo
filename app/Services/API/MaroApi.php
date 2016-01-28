<?php
/**
 * User: rbertorelli
 */

namespace App\Services\API;

use App\Facades\EspAccount;
use App\Facades\Guzzle;
/**
 * Class BaseAPI
 * @package App\Services\API
 */

class MaroApi extends BaseAPI {

    const API_URL = "http://api.maropost.com/accounts/%d/reports.json?";
    protected $apiKey;
    protected $date;
    protected $accountName;

    public function __construct($name, $espAccountId) {
        parent::__construct($name, $espAccountId);
        $creds = EspAccount::grabApiAccountNameAndKey($espAccountId);
        $this->accountName = $creds['accountName'];
        $this->apiKey = $creds['apiKey'];
    }

    public function setDate($date) {
        $this->date = $date;
    }

    protected function sendApiRequest($url) {
        return Guzzle::get($url, ['verify' => false]);
    }

    protected function constructApiUrl($page = null) {

        $baseUrl = sprintf(self::API_URL, $this->accountName);
        $baseUrl .= ('auth_token=' . $this->apiKey);
        
        if ($page) {
            $baseUrl .= '&page=' . $page;
        }
        if ($this->date) {
            $baseUrl .= '&from=' . $this->date . '&to=' . $this->date;
        }
        return $baseUrl;
    }


}