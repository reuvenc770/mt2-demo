<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/24/17
 * Time: 10:49 AM
 */

namespace App\Repositories;


use App\Models\BrontoIdMapping;

class BrontoIdMappingRepo
{
    protected $model;
    
    public function __construct(BrontoIdMapping $mapping)
    {
        $this->model = $mapping;
    }

    public function rowOrNew($id,$espAccountId){
        return $this->model->firstOrNew(['primary_id'=>$id, 'esp_account_id' => $espAccountId]);
    }

    public function getOriginalId($id,$espAccountId){
        return $this->model->where(["generated_id" => $id,"esp_account_id" => $espAccountId])->first();
    }
}