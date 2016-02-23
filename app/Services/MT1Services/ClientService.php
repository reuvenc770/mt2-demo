<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/16/16
 * Time: 2:35 PM
 */

namespace App\Services\MT1Services;


use App\Repositories\MT1Repositories\ClientRepo;
use App\Services\API\MT1Api;

use App\Services\ServiceTraits\PaginateMT1;

class ClientService
{
    use PaginateMT1;
    protected $clientRepo;

    public function __construct(ClientRepo $clientRepo, MT1Api $apiService)
    {
        $this->clientRepo = $clientRepo;
        $this->pageName  = "clients_list";
        $this->api = $apiService;
    }


    public function getAllTypes(){
        return $this->clientRepo->getClientTypes();
    }

}