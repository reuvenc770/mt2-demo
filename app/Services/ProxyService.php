<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:58 PM
 */

namespace App\Services;


use App\Repositories\ProxyRepo;
use App\Services\ServiceTraits\PaginateList;
use Log;
class ProxyService
{
    use PaginateList;
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

    public function getAllActive(){
        return $this->proxyRepo->getAllActive();
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

    public function getModel(){
        return $this->proxyRepo->getModel();
    }

    public function toggleRow($id, $direction){
        return $this->proxyRepo->toggleRow($id,$direction);
    }

    //override return model so its a builder and not Collection
    public function getType(){
        return "Proxy";
    }

    public function tryToDelete($id){
        $canBeDeleted =  $this->proxyRepo->canBeDeleted($id);
        if($canBeDeleted === true){
            $this->proxyRepo->delete($id);
            return true;
        } else{
            return $canBeDeleted;
        }
    }

}