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
        return $this->model->where("active",1)->get();
    }
}