<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/10/17
 * Time: 11:00 AM
 */

namespace app\Repositories;


use App\Models\AWeberList;

class AWeberListRepo
{

    protected $model;

    public function __construct(AWeberList $lists)
    {
        $this->model = $lists;
    }
    
    public function getActiveLists(){
        return $this->model->where("is_active",1)->get();
    }

    public function upsertList($list){
        return $this->model->updateOrCreate(["internal_id" => $list['internal_id']],$list);
    }
}