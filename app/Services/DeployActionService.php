<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 5/18/16
 * Time: 2:26 PM
 */

namespace App\Services;


use App\Models\StandardReport;
use App\Repositories\CampaignActionsRepo;
use App\Repositories\DeployActionRepo;

class DeployActionService
{
    protected $deployActionRepo;

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
        $columnName = "last_success_{$type}";
    $entry = array (
        'column'          => $columnName,
        'esp_account_id' => $esp_account_id,
        'esp_internal_id' => $esp_internal_id,
    );
    $this->deployActionRepo->updateDeployAction($entry);


    }

    public function recordFailedRun($esp_account_id, $esp_internal_id, $type){
        $columnName = "last_fail_{$type}";
        $entry = array (
            'column'          => $columnName,
            'esp_account_id' => $esp_account_id,
            'esp_internal_id' => $esp_internal_id,
        );
        $this->deployActionRepo->updateDeployAction($entry);


    }

}