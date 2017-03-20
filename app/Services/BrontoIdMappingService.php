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
    CONST DUMB_ID = "0bce03ee";
    public function __construct(BrontoIdMappingRepo $mappingRepo)
    {
        $this->repo = $mappingRepo;
    }

    public function returnOrGenerateID($id,$espAccountId){
        if(Cache::has("Bronto.{$espAccountId}.mapping.{$id}")) {
            return Cache::get("Bronto.{$espAccountId}.mapping.{$id}");
        } else {
            $row = $this->repo->rowOrNew($id, $espAccountId);
            if (!$row->exists) {
                $row->generated_id = rand(100000, 999999);
                $row->save();
            }
            Cache::put("Bronto.{$espAccountId}.mapping.{$id}",$row->generated_id);
            return $row->generated_id;
        }
    }

    public function returnOriginalId($id, $espAccountId){
       if(Cache::has("Bronto.{$espAccountId}.{$id}")){
           return Cache::get("Bronto.{$espAccountId}.{$id}");
       } else {
           $mapping = $this->repo->getOriginalId($id, $espAccountId);
           Cache::put("Bronto.{$espAccountId}.{$id}",$mapping->primary_id);
           return $mapping->primary_id;
       }
    }

    public function makeDumbInternalId($id,$espAccountId){
        $realId = $this->returnOriginalId($id,$espAccountId);
        if(isset($realId)){
            return $realId;
        } else {
            $converted = base_convert($id, 10, 16);
            $padded = str_pad($converted, 28, '0', STR_PAD_LEFT);
            return self::DUMB_ID . $padded;
        }
    }
}