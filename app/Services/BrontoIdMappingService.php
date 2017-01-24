<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/24/17
 * Time: 10:50 AM
 */

namespace App\Services;


use App\Repositories\BrontoIdMappingRepo;
use Cache;
class BrontoIdMappingService
{
    protected $repo;

    public function __construct(BrontoIdMappingRepo $mappingRepo)
    {
        $this->repo = $mappingRepo;
    }

    public function returnOrGenerateID($id,$espAccountId){
       $row = $this->repo->rowOrNew($id,$espAccountId);
        if($row->exists){
            return $row->generated_id;
        } else {
            $row->generated_id = rand(100000,999999);
            $row->save();
            return $row->generated_id;
        }
    }

    public function returnOriginalId($id, $espAccountId){
       if(Cache::has("Bronto.{$espAccountId}.{$id}")){
           return Cache::get("Bronto.{$espAccountId}.{$id}");
       } else {
           $mapping = $this->repo->getOriginalId($id, $espAccountId);
           Cache::put("Bronto.{$espAccountId}.{$id}",$mapping->primary_id);
       }
    }
}