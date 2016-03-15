<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EmailRepo;
use App\Repositories\ActionRepo;
use Illuminate\Support\Facades\Event;
use App\Services\Interfaces\IDataService; #change this
use League\Csv\Reader;

class CsvDeliverableService {

    private $emailRepo;
    private $emailActionsRepo;
    private $actionTypeRepo;
    private $actionMap = array(
        'clicks' => 'clicker',
        'opens' => 'opener',
        'delivered' => 'deliverable',
    );
    private $this->campaignId; // want to keep this across rows
    private $mapping = array();
    private $actionId;


    public function __construct(EmailActionsRepo $emailActionsRepo, EmailRepo $emailRepo, ActionRepo $actionTypeRepo, DeliverableMappingRepo $mappingRepo) {
        $this->emailActionsRepo = $emailActionsRepo;
        $this->emailRepo = $emailRepo;
        $this->actionTypeRepo = $actionTypeRepo;
        $this->mappingRepo = $mappingRepo;
    }

    public function setCsvToFormat($espAccountId, $action, $filePath) {
        $returnArray = array();
        $this->setMapping($espAccountId);

        $mappedAction = $this->mapActionToActionTableName($action);
        $actionId = $this->actionTypeRepo->getActionId($mappedAction);
        
        $reader = Reader::createFromPath($filePath);
        $data = $reader->fetchAssoc();

        foreach ($data as $row) {

            $email = $this->getEmail($row);
            $campaignId = $this->getCampaignId($row, $filePath);
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

        return $returnArray;
    }


    private function setMapping($espAccountId) {
        $this->mapping = $this->mappingRepo->getMapping($espAccountId);
    }


    private function getEmail($row) {
        if ((int)$this->mapping['email_pos'] >= 0) {
            return $row[(int)$this->mapping['email_pos']];
        }
        else {
            throw new \Exception("Email mapping not found for this account");
        } 
    }


    private function getEmailId($row, $email) {
        if ((int)$this->mapping['eid_pos'] >= 0) {
            return $row[(int)$this->mapping['eid_pos']];
        }
        else {
            return $this->emailRepo->getEmailId($email);
        } 
    }


    private function getClientId($emailId) {
        return $this->emailRepo->getAttributedClient($emailId);
    }


    private function getCampaignId($row, $path) {
        if (isset($this->campaignId)) {
            // Assuming no change of campaign within file ...
            return $this->campaignId;
        }
        elseif ((int)$this->mapping['campaign_pos'] >= 0) {
            $this->campaignId = $row[(int)$this->mapping['campaign_pos']];
            return $this->campaignId;
        }
        else {
            return $this->getCampaignIdFromFileName($path);
        }
    }


    private function getDateTime($row) {
        if ((int)$this->mapping['campaign_pos'] >= 0) {
            return $row[(int)$this->mapping['campaign_pos']];
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

        $parts = explode('/', $path);
        $fileName = $parts[sizeof($parts) - 1];

        $nameParts = explode('_', $fileName);
        echo $nameParts[0];
        if (is_numeric($nameParts[0])) {
            return (int)$nameParts[0];
        }
        else {
            throw new \Exception("The filename $fileName is invalid");
        }
    }
  
}