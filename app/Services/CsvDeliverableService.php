<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EmailRepo;
use App\Repositories\ActionRepo;
use Illuminate\Support\Facades\Event;
use App\Services\Interfaces\IDataService; #change this
use League\Csv\Reader;
use Carbon\Carbon;

class CsvDeliverableService {

    private $emailRepo;
    private $emailActionsRepo;
    private $actionTypeRepo;
    private $actionMap = array(
        'clicks' => 'clicker',
        'opens' => 'opener',
        'delivered' => 'deliverable',
    );
    private $campaignId; // want to keep this across rows
    private $mapping = array();
    private $actionId;


    public function __construct(EmailActionsRepo $emailActionsRepo, EmailRepo $emailRepo, ActionRepo $actionTypeRepo, $mapping) {
        $this->emailActionsRepo = $emailActionsRepo;
        $this->emailRepo = $emailRepo;
        $this->actionTypeRepo = $actionTypeRepo;
        $this->mapping = $mapping;
    }

    public function setCsvToFormat($espAccountId, $action, $filePath) {
        $returnArray = array();

        $mappedAction = $this->mapActionToActionTableName($action);
        $actionId = $this->actionTypeRepo->getActionId($mappedAction);
        
        $reader = Reader::createFromPath($filePath);
        $map = explode(',', $this->mapping);
        $data = $reader->fetchAssoc($map);

        foreach ($data as $row) {

            if (filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $email = $row['email'];
                $campaignId = isset($row['campaign_id']) ? $row['campaign_id'] : $this->getCampaignIdFromFileName($filePath);
                $emailId = $this->getEmailId($row);
                $clientId = $this->getClientId($row);
                $datetime = $this->getDateTime($row);

                $returnArray[] = array(
                    'email_id' => $emailId,
                    'client_id' => $clientId,
                    'esp_account_id' => $espAccountId,
                    'action_id' => $actionId,
                    'campaign_id' => $campaignId,
                    'datetime' => $datetime,
                );
            }

        }

        return $returnArray;
    }

    private function getEmail($row) {
        if ((int)$this->mapping['email_pos'] >= 0) {
            return $row[(int)$this->mapping['email_pos']];
        }
        else {
            throw new \Exception("Email mapping not found for this account");
        } 
    }


    private function getEmailId($row) {
        if (isset($row['email_id'])) {
            return $row['email_id'];
        }
        else {
            return $this->emailRepo->getEmailId($row['email']);
        } 
    }


    private function getClientId($row) {
        return $this->emailRepo->getAttributedClient($row['email']);
    }


    private function getDateTime($row) {
        if (isset($row['datetime'])) {
            $date = trim($row['datetime']);
            // Currently the only format, but this may need to be updated/generalized
            return Carbon::createFromFormat('d/m/Y H:i:s', $date)->toDateTimeString();
        }
        else {
            // It's ok to return blank here
            return '';
        }
    }


    public function insertDeliverableCsvActions($data) {
        foreach ($data as $row) {
            $this->emailActionsRepo->insertAction($row);
        }
    }


    private function getCampaignIdFromFileName($path) {
        /*
        Name will be something like
        /var/www/storage/app/BH001/clicks/1301931_BH001_YAH_US_60OC_CPM_PROGRESSIVE.csv

        Need to get 1301931_BH001_YAH_US_60OC_CPM_PROGRESSIVE.csv

        And from that, grab and return 1301931
        */

        if (isset($this->campaignId)) {
            // Assuming no change of campaign within file ...
            return $this->campaignId;
        }

        $parts = explode('/', $path);
        $fileName = $parts[sizeof($parts) - 1];

        $nameParts = explode('_', $fileName);
        if (is_numeric($nameParts[0])) {
            return (int)$nameParts[0];
        }
        else {
            throw new \Exception("The filename $fileName is invalid");
        }
    }

    private function mapActionToActionTableName($action) {
        return $this->actionMap[$action];
    }
  
}