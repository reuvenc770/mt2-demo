<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/10/17
 * Time: 11:03 AM
 */

namespace app\Services;


use app\Repositories\AWeberListRepo;

class AWeberListService
{
    protected $repository;

    public function __construct(AWeberListRepo $listRepo)
    {
        $this->repository = $listRepo;
    }


    public function getActiveLists(){
        return $this->repository->getActiveLists();
    }

}