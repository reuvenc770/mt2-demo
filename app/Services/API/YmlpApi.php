<?php
/**
 * User: rbertorelli
 */

namespace App\Services\API;

use App\Facades\EspApiAccount;
use App\Facades\Guzzle;
use Carbon\Carbon;
use App\Library\YMLP\YMLP_API;

class YmlpApi extends EspBaseAPI {

  const REPORT_API_URL = "";
  const OPENS_URL = "";
  const BOUNCES_URL = "";
  const CLICKS_URL = "";
  const ESP_NAME = "YMLP";

  protected $apiSdk;
  protected $startDate;

  public function __construct ($espAccountId) {
    parent::__construct(self::ESP_NAME, $espAccountId);
    $creds = EspApiAccount::grabApiUsernameWithPassword($espAccountId);
    $this->apiSdk = new YMLP_API($creds['password'], $creds['userName'], true);
  }

  public function setDate($date) {
    $this->startDate = $date;
  }

  public function getId () { return $this->espAccountId; }

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

  public function getDeliverableStat($stat, $newsletterId) {
    $page = 1;
    $done = false;
    $finalOutput = array();

    while (!$done) {
      $output = json_decode($this->callDeliverableApiCall($stat, $newsletterId, $page),true);
      if ($output) {
        $finalOutput = array_merge($finalOutput, $output);
        $page++;
      }
      else {
        $done = true;
      }
    }

    return $finalOutput;
  }

  private function callDeliverableApiCall($stat, $newsletterId, $page) {
    $numberPerPage = 1000;
    $onlyUnique = false;

    switch ($stat) {
      case 'delivered':
        $output = $this->apiSdk->ArchiveGetDelivered($newsletterId, $page, $numberPerPage);
        break;

      case 'bounced':
        $hardBounces = 1;
        $softBounces = 0;
        $output = $this->apiSdk->ArchiveGetBounces($newsletterId, $hardBounces, $softBounces, $page, $numberPerPage);
        break;

      case 'opened':
        $output = $this->apiSdk->ArchiveGetOpens($newsletterId, $onlyUnique, $page, $numberPerPage);
        break;

      case 'clicked':
        $links = '';
        $output = $this->apiSdk->ArchiveGetClicks($newsletterId, $links, $onlyUnique, $page, $numberPerPage);
        break;

      default:
        throw new \Exception ('Invalid action type for YMLP');
    }
    if ($this->apiSdk->ErrorMessage) {
      throw new \Exception('Cannot connect to YMLP API');
    }

    return $output;
  }

  public function callUnsubApi($startDate, $stopDate)
  {
    $page = 1;
    $done = false;
    $finalOutput = array();
    while (!$done) {
      $output = json_decode($this->apiSdk->ContactsGetUnsubscribed("",$page, 1000, $startDate, $stopDate),true);
      if ($output) {
        $finalOutput = array_merge($finalOutput, $output);
        $page++;
      }
      else {
        $done = true;
      }
    }

    return $finalOutput;

  }
}