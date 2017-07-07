<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 5/18/16
 * Time: 2:26 PM
 */

namespace App\Services;


use App\Models\StandardReport;
use App\Repositories\DeployActionRepo;

class DeployActionService
{
    protected $deployActionRepo;
    protected $types = ["open","click","optout","bounce","complaint","deliverable"];
    public function __construct(DeployActionRepo $deployActionRepo)
    {
        $this->deployActionRepo = $deployActionRepo;
    }


    public function initDeployActions(StandardReport $standardReport){
        $dataToInsert = array(
          'esp_account_id' => $standardReport->esp_account_id,
          'esp_internal_id' => $standardReport->esp_internal_id,
        );

       return $this->deployActionRepo->insertNewEntry($dataToInsert);
    }

    public function recordSuccessRun($esp_account_id, $esp_internal_id, $type){
        $entry = $this->returnEntry($esp_account_id, $esp_internal_id, $type, 'success');
    $this->deployActionRepo->updateDeployAction($entry);
    }

    public function recordFailedRun($esp_account_id, $esp_internal_id, $type){
        $entry = $this->returnEntry($esp_account_id, $esp_internal_id, $type, 'fail');
        $this->deployActionRepo->updateDeployAction($entry);
    }

    public function recordSuccessRunArray($esp_account_id, $internalIds, $type){
        foreach($internalIds as $esp_internal_id){
            $entry = $this->returnEntry($esp_account_id, $esp_internal_id, $type, 'success');
            $this->deployActionRepo->updateDeployAction($entry);
        }

    }

    public function recordFailedRunArray($esp_account_id, $internalIds, $type){
        foreach($internalIds as $esp_internal_id) {
            $entry = $this->returnEntry($esp_account_id, $esp_internal_id, $type, 'fail');
            $this->deployActionRepo->updateDeployAction($entry);
        }
    }

    public function recordAllSuccess($esp_account_id, $esp_internal_id){
        foreach($this->types as $type) {
            $entry = $this->returnEntry($esp_account_id, $esp_internal_id, $type, 'success');
            $this->deployActionRepo->updateDeployAction($entry);
        }
    }

    public function recordAllFail($esp_account_id, $esp_internal_id){
        foreach($this->types as $type) {
        $entry = $this->returnEntry($esp_account_id, $esp_internal_id, $type, 'fail');
        $this->deployActionRepo->updateDeployAction($entry);
        }
    }

    private function returnEntry($esp_account_id, $esp_internal_id, $type, $boolName){
        $columnName = "last_{$boolName}_{$type}";
        return $entry = array(
            'column' => $columnName,
            'esp_account_id' => $esp_account_id,
            'esp_internal_id' => $esp_internal_id,
        );
    }
}