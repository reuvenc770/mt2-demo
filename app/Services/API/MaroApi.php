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

class BaseAPI implements IReportService {

    const API_URL = "http://api.maropost.com/accounts/%d/reports.json?";
    protected $apiKey;
    protected $date;

    public function __construct($name, $accountNumber) {
        parent::__construct($name, $accountNumber);
        $creds = EspAccount::getFirstKey($accountNumber);
        $apiKey = $creds['key_1'];
    }

    public function setDate($date) {
        $this->date = $date;
    }

    protected function sendApiRequest($url) {
        return Guzzle::request('GET', $apiUrl);
    }

    protected function constructApiUrl($page = null) {

        $baseUrl = sprintf(self::API_URL, $this->accountNumber);
        $baseUrl .= 'auth_token=' . $this->apiKey;

        if ($page) {
            $baseUrl .= '&page=' . $page;
        }
        if ($this->date) {
            $baseUrl .= '&from=' . $this->date . '&to=' . $this->date;
        }
        return $baseUrl;
    }


}