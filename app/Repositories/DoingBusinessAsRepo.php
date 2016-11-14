<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:54 PM
 */

namespace App\Repositories;


use App\Models\DoingBusinessAs;

class DoingBusinessAsRepo
{
    protected $doingBusinessAs;

    public function __construct(DoingBusinessAs $businessAs)
    {
        $this->doingBusinessAs = $businessAs;

    }
    public function insertRow($data){
      return $this->doingBusinessAs->create($data);
    }

    public function getAll(){
        return $this->doingBusinessAs->all();
    }
    public function getAllActive(){
        return $this->doingBusinessAs->where('status',1)->get();
    }

    public function fetch($id){
        return $this->doingBusinessAs->find($id);
    }

    public function updateAccount ( $id , $accountData ) {
        return $this->doingBusinessAs->find( $id )->update( $accountData);
    }

    public function toggleRow($id, $direction){
        return $this->doingBusinessAs->find($id)->update(["status" => $direction]);
    }

    public function getModel($searchData){
        $query = $this->doingBusinessAs;
        if('' !== $searchData) {
            $query = $this->mapQuery($searchData, $query);
        }
        return $query;
    }

    private function mapQuery($searchData, $query){
        $searchData = json_decode($searchData, true);

        if (isset($searchData['dba_name'])) {
            $query->where('dba_name','LIKE', $searchData['dba_name'].'%');
        }

        if (isset($searchData['registrant_name'])) {
            $query->where('deploys.esp_account_id','LIKE', $searchData['dba_name'].'%');
        }

        if (isset($searchData['dba_email'])) {
            $query->where('deploys.id', (int)$searchData['deployId']);
        }

        if (isset($searchData['address'])) {
            $query->where('deploys.deployment_status', (int)$searchData['status']);
        }

        if (isset($searchData['entity_name'])) {
            $query->where('offers.name','LIKE', $searchData['offerNameWildcard'] . '%');
        }

        return $query;
    }
}