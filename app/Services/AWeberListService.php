<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/10/17
 * Time: 11:03 AM
 */

namespace App\Services;


use App\Repositories\AWeberListRepo;

class AWeberListService
{
    protected $repository;

    public function __construct(AWeberListRepo $listRepo)
    {
        $this->repository = $listRepo;
    }
    

    public function getAllListsByAccount($espAccountId){
        return $this->repository->getListsByAccount($espAccountId);
    }


    public function getActiveLists(){
        return $this->repository->getActiveLists();
    }
    
    public function updateOrAddList($list,$espAccountId){
        $formatted = array(
            "internal_id"                 => $list->id,
            "esp_account_id"              => $espAccountId,
            "name"                        => $list->name,
            "total_subscribers"           => $list->total_subscribers,
            "subscribers_collection_link" => $list->subscribers_collection_link,
            "campaigns_collection_link"   => $list->campaigns_collection_link,
        );
        return $this->repository->upsertList($formatted);
    }
    
    public function updateListStatuses($ids){
       return $this->repository->massUpdateStatus($ids);
    }

}