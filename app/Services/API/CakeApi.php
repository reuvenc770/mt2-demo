<?php

namespace App\Services\API;

use App\Facades\Guzzle;
use App\Services\Interfaces\IApi;

use Log;

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

  public function sendApiRequest( $data = null ) {
    $url = $this->constructApiUrl( $data );

    return Guzzle::get($url);
  }

  private function constructApiUrl( $data = null ) {
    $fields = [
        "apiKey" => self::API_KEY ,
        "dtStart" => $this->startDate ,
        "dtEnd" => $this->endDate
    ];

    if ( !is_null( $data ) ) {
        $fields += $data;        
    }

    return self::ENDPOINT . http_build_query( $fields ); 
  }

  public function __get($prop) {
    return isset($this->$prop) ? $this->$prop : '';
  }
}
