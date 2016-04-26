<?php

namespace App\Services;
use App\Repositories\EmailActionsRepo;
use App\Repositories\EmailRepo;
use App\Repositories\ActionRepo;
use App\Repositories\StandardApiReportRepo;
use App\ServicesEmailRecordService;
use League\Csv\Reader;

class CsvDeliverableService {

    private $emailRepo;
    private $emailActionsRepo;
    private $actionTypeRepo;
    private $reportRepo;
    private $actionMap = array(
        'clicks' => 'clicker',
        'opens' => 'opener',
        'delivered' => 'deliverable',
    );
    private $campaignId; // want to keep this across rows
    private $mapping = array();
    private $actionId;


    public function __construct(EmailActionsRepo $emailActionsRepo, EmailRepo $emailRepo, ActionRepo $actionTypeRepo, StandardApiReportRepo $reportRepo, $mapping) {
        $this->emailActionsRepo = $emailActionsRepo;
        $this->emailRepo = $emailRepo;
        $this->actionTypeRepo = $actionTypeRepo;
        $this->mapping = $mapping;
        $this->reportRepo = $reportRepo;
    }

    public function setCsvToFormat($filePath) {
        $returnArray = array();

        $reader = Reader::createFromPath(storage_path('app').DIRECTORY_SEPARATOR.$filePath);
        $map = explode(',', $this->mapping);
        $data = $reader->fetchAssoc($map);
        $returnArray = array();
        foreach ($data as  $row){
            $deployId = isset($row['deploy_id']) ? $row['deploy_id'] : $this->getDeployIdFromFileName($filePath);
            $returnArray[] = array (
                'email_address' => $row['email_address'],
                'deploy_id' => $deployId,
                'esp_internal_id' => isset($row['esp_internal_id']) ? $row['esp_internal_id'] : $this->getEspInternalIdFromDeployId($deployId),
                'datetime' => isset($row['datetime']) ? $row['datetime'] : $this->getDateTimeFromDeployId($deployId)
            );
        }
        return $returnArray;
    }



    private function getDeployIdFromFileName($path) {
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
            echo (int)$nameParts[0];
            return (int)$nameParts[0];
        }
        else {
            throw new \Exception("The filename $fileName is invalid");
        }
    }


    private function getEspInternalIdFromDeployId($deployId){
        $return =  $this->reportRepo->getInternalIdFromDeployId($deployId);
        if (!$return){
            throw new \Exception("Have not retrieved campaign with Deploy ID of {$deployId}");
            }
        return $return;
    }

    private function getDateTimeFromDeployId($deployId){
        $return =  $this->reportRepo->getDateFromDeployId($deployId);
        if (!$return){
            throw new \Exception("Have not retrieved campaign with Deploy ID of {$deployId}");
        }
        return $return;
    }

}