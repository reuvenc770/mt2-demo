<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/16/16
 * Time: 2:59 PM
 */

namespace App\Services\MT1Services;


use App\Repositories\MT1Repositories\ClientStatsGroupingRepo;

class ClientStatsGroupingService
{
    protected $clientGroupingRepo;

    public function __construct(ClientStatsGroupingRepo $groupingRepo)
    {
        $this->clientGroupingRepo = $groupingRepo;
    }

    public function getListGroups(){
       return $this->clientGroupingRepo->getGroupingNameAndLabels();
    }
}