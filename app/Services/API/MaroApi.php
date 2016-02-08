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
    protected $apiKey;
    protected $priorDate;
    protected $date;
    protected $account;


    public function __construct($name, $espAccountId) {
        parent::__construct($name, $espAccountId);
        $creds = EspApiAccount::grabApiAccountIdAndKey($espAccountId);
        $this->account = $creds['account'];
        $this->apiKey = $creds['apiKey'];
    }

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


}
