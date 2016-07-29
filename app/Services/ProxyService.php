<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:58 PM
 */

namespace App\Services;


use App\Repositories\ProxyRepo;
use Log;
class ProxyService
{
    protected $proxyRepo;

    public function __construct(ProxyRepo $proxyRepo)
    {
        $this->proxyRepo = $proxyRepo;
    }
    public function insertRow($request){
        try {
            return $this->proxyRepo->insertRow($request);
        } catch(\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAll(){
        return $this->proxyRepo->getAll();
    }

    public function getProxy($id){
        return $this->proxyRepo->fetch($id);
    }

    public function updateAccount($id, $accountData){
        try {
            return $this->proxyRepo->updateAccount($id, $accountData);
        } catch( \Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }


    public function getAllByType($type){
        try {
            return $this->proxyRepo->getRowsByType($type);
        } catch( \Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

}