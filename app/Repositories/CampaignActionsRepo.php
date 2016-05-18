<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 5/18/16
 * Time: 2:26 PM
 */

namespace App\Repositories;


use App\Models\CampaignActionsEntry;

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

}