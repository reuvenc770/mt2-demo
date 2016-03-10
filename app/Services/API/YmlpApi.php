<?php
/**
 * User: rbertorelli
 */

namespace App\Services\API;

use App\Facades\EspApiAccount;
use App\Facades\Guzzle;
use Carbon\Carbon;
use App\Library\YMLP\YMLP_API;

class YmlpApi extends EspBaseApi {

  const REPORT_API_URL = "";
  const OPENS_URL = "";
  const BOUNCES_URL = "";
  const CLICKS_URL = "";

  protected $apiSdk;
  protected $startDate;

  public function __construct($name, $espAccountId) {
    parent::__construct($name, $espAccountId);
    $creds = EspApiAccount::grabApiUsernameWithPassword($espAccountId);
    $this->apiSdk = new YMLP_API($creds['password'], $creds['userName'], true);
  }

  public function setDate($date) {
    $this->startDate = $date;
  }

  public function sendApiRequest() {
    $page = 1;
    $numberPerPage = 1000;
    $done = false;
    $finalOutput = array();

    while (!$done) {
      $output = $this->apiSdk->ArchiveGetList($page, $numberPerPage, $this->startDate);

      if ($this->apiSdk->ErrorMessage) {
        throw new \Exception('Cannot connect to YMLP API');
      }
      else {
        if ($output) {
          $finalOutput = array_merge($finalOutput, $output);
          $page++;
        }
        else {
          $done = true;
        }
      }
    }

    return $finalOutput;
  }

  public function getDeliveryReport() {

  }
}