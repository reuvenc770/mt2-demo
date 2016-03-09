<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/8/16
 * Time: 3:44 PM
 */

namespace App\Services\API;
use App\Facades\EspApiAccount;
use GuzzleHttp\Client;

class GetResponseApi extends EspBaseAPI
{
    const API_URL = "https://api.getresponse.com/v3/";
    protected $apiKey;
    protected $date;
    protected $query = array();
    protected $action;
    public function __construct($name, $espAccountId)
    {
        parent::__construct($name, $espAccountId);
        $this->apiKey = EspApiAccount::grabApiKey($espAccountId);
        $this->query = array("query");
        $this->guzzle = new Client(['http_errors' => false, 'base_uri' => self::API_URL,
                'headers' => ['Content-type' => 'application/json','X-Auth-Token' => "api-key {$this->apiKey}"]]
        );

    }

    public function sendApiRequest()
    {
        $data =$this->guzzle->get($this->action,['query' => $this->query]);
        $data = $data->getBody()->getContents();
        return json_decode($data, true);
    }

    public function setAction($action){
        $this->action = $action;
    }

    public function setQuery($query){
        $this->query = $query;
    }
}