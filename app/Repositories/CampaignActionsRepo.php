<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 5/18/16
 * Time: 2:26 PM
 */

namespace App\Repositories;


use App\Models\CampaignActionsEntry;
use Carbon\Carbon;

class CampaignActionsRepo
{
    protected $campaignAction;

    public function __construct(CampaignActionsEntry $campaignActionsEntry)
    {
        $this->campaignAction = $campaignActionsEntry;
    }

    public function insertNewEntry($entryData){
        return $this->campaignAction->create($entryData);
    }

    public function updateCampaignAction($entry){
        return $this->campaignAction->where('esp_account_id', $entry['esp_account_id'])
                                    ->where('esp_internal_id', $entry['esp_internal_id'])
                                    ->update($entry['column'], Carbon::now()->toDateTimeString());
    }

}