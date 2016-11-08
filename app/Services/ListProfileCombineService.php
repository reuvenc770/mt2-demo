<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 11/7/16
 * Time: 3:37 PM
 */

namespace App\Services;


use App\Repositories\ListProfileCombinesRepo;
use App\Services\ServiceTraits\PaginateList;

class ListProfileCombineService
{

    protected $listProfileCombinesRepo;

    public function __construct(ListProfileCombinesRepo $combinesRepo)
    {
        $this->listProfileCombinesRepo = $combinesRepo;
    }

    public function getAll(){
       return $this->listProfileCombinesRepo->getAll();
    }

    //TODO: begs to be in a trait as well but it means being more generic with repo names.. but then getModel could be refactored as well
    //maybe an interface to make sure toggleRow is always around?
    public function toggleRow($id, $direction){
        return $this->listProfileCombinesRepo->toggleRow($id,$direction);
    }

    public function insertCombine($insertData, $listProfileIds){
        $item = $this->listProfileCombinesRepo->insertRow($insertData);
        foreach($listProfileIds as $listProfileId){
            $this->listProfileCombinesRepo->attachPivot($item,$listProfileId);
        }
    }

}