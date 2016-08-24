<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/16/16
 * Time: 2:53 PM
 */

namespace App\Repositories\MT1Repositories;
use \App\Models\MT1Models\ClientStatsGrouping;
class ClientStatsGroupingRepo
{
    protected $clientGrouping;

    public function __construct(ClientStatsGrouping $clientStatsGrouping)
    {
        $this->clientGrouping = $clientStatsGrouping;
    }

    public function getGroupingNameAndLabels(){
        return $this->clientGrouping->select('clientStatsGroupingID as value' , 'clientStatsGroupingName as name' )
            ->orderBy("name")->get();
    }

    public function getListOwnerName ( $listOwnerId ) {
        return $this->clientGrouping->where( 'clientStatsGroupingID' , $listOwnerId )->pluck( 'clientStatsGroupingLabel' )->pop();
    } 
}
