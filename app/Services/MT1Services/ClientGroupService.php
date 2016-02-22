<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/22/16
 * Time: 1:04 PM
 */

namespace App\Services\MT1Services;


use App\Repositories\MT1Repositories\ClientGroupRepo;

class ClientGroupService
{
    protected $clientGroupRepo;

    public function __construct(ClientGroupRepo $clientGroupRepo)
    {
        $this->clientGroupRepo = $clientGroupRepo;
    }


    public function getAllNames(){
        return $this->clientGroupRepo->getAllClientGroups();
    }

    public function getClientsForClientGroup($id){
        return $this->clientGroupRepo->getAllClientsForGroup($id);
    }
}