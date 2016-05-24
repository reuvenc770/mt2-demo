<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 5/18/16
 * Time: 2:26 PM
 */

namespace App\Repositories;


use App\Models\DeployActionEntry;
use Carbon\Carbon;

class DeployActionRepo
{
    protected $deployAction;

    public function __construct(DeployActionEntry $deployActionEntry)
    {
        $this->deployAction = $deployActionEntry;
    }

    public function insertNewEntry($entryData){
        return $this->deployAction->create($entryData);
    }

    public function updateDeployAction($entry){
        return $this->deployAction->where('esp_account_id', $entry['esp_account_id'])
                                    ->where('esp_internal_id', $entry['esp_internal_id'])
                                    ->update([$entry['column'] => Carbon::now()->toDateTimeString()]);
    }

}