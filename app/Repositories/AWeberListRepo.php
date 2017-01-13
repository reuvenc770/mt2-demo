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

    public function getListsByAccount($espAccountId){
        return $this->model->where("esp_account_id",$espAccountId)->get();
    }
    
    public function getActiveLists(){
        return $this->model->where("is_active",1)->get();
    }

    public function upsertList($list){
        return $this->model->updateOrCreate(["internal_id" => $list['internal_id']],$list);
    }
    //there is probably a better way to do this, but its such a 1 off piece of code
    public function massUpdateStatus($ids){
        $this->model->query()->update(["is_active"=>1]); //set them all active;
        $this->model->whereIn('id',$ids)->update(['is_active' => 0]);
        return true;
    }
}