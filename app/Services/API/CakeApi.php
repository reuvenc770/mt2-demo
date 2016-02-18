<?php

namespace App\Services\API;

use App\Facades\Guzzle;
use App\Services\Interfaces\IApi;

class CakeApi implements IApi {

  // A temporary stub because we will likely be internalizing this
  const ENDPOINT = "http://caridan.ampxl.net/app/websvc/cake/mt2/index.php?";
  const API_KEY = 'F9437Yjf*udfk39';
  private $startDate;
  private $endDate;

  public function __construct($startDate, $endDate) {
    $this->startDate =  $startDate;
    $this->endDate =  $endDate;
  }

  public function sendApiRequest() {
    $url = $this->constructApiUrl();
    return Guzzle::get($url);
  }

  private function constructApiUrl() {
    return self::ENDPOINT . 'apiKey=' . self::API_KEY 
    . '&dtStart=' . $this->startDate 
    . '&endDate=' . $this->endDate;
  }

  public function __get($prop) {
    return isset($this->$prop) ? $this->$prop : '';
  }
}