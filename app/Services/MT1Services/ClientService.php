<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/16/16
 * Time: 2:35 PM
 */

namespace App\Services\MT1Services;


use App\Repositories\MT1Repositories\ClientRepo;

class ClientService
{
    protected $clientRepo;

    public function __construct(ClientRepo $clientRepo)
    {
        $this->clientRepo = $clientRepo;
    }


    public function getAllTypes(){
        return $this->clientRepo->getClientTypes();
    }

}