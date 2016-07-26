<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:54 PM
 */

namespace App\Repositories;

use App\Models\Proxy;

class ProxyRepo
{
    protected $proxy;

    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    public function insertRow($data){
        return $this->proxy->insert($data);
    }

    public function getAll(){
        return $this->proxy->all();
    }

    public function fetch($id){
        return $this->proxy->find($id);
    }

    public function updateAccount ( $id , $accountData ) {
        return $this->proxy->where( 'id' , $id )->update($accountData);
    }

    public function getRowsByType($type){
        return $this->proxy->where("domain_type",$type)->get();
    }

}