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
        return $this->proxy->create($data);
    }

    public function getAll(){
        return $this->proxy->all();
    }

    public function getAllActive(){
        return $this->proxy->where("status",1)->get();
    }

    public function fetch($id){
        return $this->proxy->find($id);
    }

    public function updateAccount ( $id , $accountData ) {
        return $this->proxy->find($id )->update($accountData);
    }

    public function getModel(){
        return $this->proxy;
    }

    public function toggleRow($id, $direction){

        return $this->proxy->find($id)->update(["status" => $direction]);
    }


}