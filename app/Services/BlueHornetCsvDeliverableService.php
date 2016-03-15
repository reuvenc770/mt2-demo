<?php

namespace App\Services;
use App\Services\AbstractCsvDeliverableService;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EmailRepo;
use App\Repositories\ActionRepo;
use Illuminate\Support\Facades\Event;
use App\Services\Interfaces\IDataService; #change this
use League\Csv\Reader;

class BlueHornetCsvDeliverableService extends AbstractCsvDeliverableService {


    public function __construct(EmailActionsRepo $emailActionsRepo, EmailRepo $emailRepo, ActionRepo $actionRepo) {
        parent::__construct($emailActionsRepo, $emailRepo, $actionRepo);
    }

    protected function grabCsvDeliverableMapping($somethingUndetermined) {
        /**
         Given just an email address. We need:
            - email_id >> get from email
            - client_id >> need to get from attribution
            - esp_id >> can get from API
            - action_id >> can get from the action type
            - date, time, datetime >> might need to set a default
            - campaign_id >> probably can get from filename in this case


         */


    }

    public function setCsvToFormat($espAccountId, $action, $filePath) {
        echo $filePath;
        $returnArray = array();

        $mappedAction = $this->mapActionToActionTableName($action);
        $actionId = $this->actionTypeRepo->getActionId($mappedAction);
        $campaignId = $this->getCampaignIdFromFileName($filePath);        
        
        $reader = Reader::createFromPath($filePath);
        $data = $reader->fetchAssoc();

        foreach ($data as $row) {
            $email = $row['email'];
            $emailId = $this->emailRepo->getEmailId($email);
            $clientId = $this->emailRepo->getAttributedClient($emailId);

            $returnArray[] = array(
                'email_id' => $emailId,
                'client_id' => $clientId,
                'esp_account_id' => $espAccountId,
                'action_id' => $actionId,
                'campaign_id' => $campaignId,
                'date' => '',
                'time' => '',
                'datetime' => '',
            );
        }

        return $returnArray;
    }


    public function insertDeliverableCsvActions($data) {
        foreach ($data as $row) {
            /*
            echo "Insert: email {$row['email_id']}, 
            client {$row['client_id']}, action {$row['action_id']}, 
            campaign {$row['campaign_id']}, esp account {$row['esp_account_id']}" . PHP_EOL;
            */
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