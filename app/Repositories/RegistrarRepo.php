<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 1:55 PM
 */

namespace App\Repositories;


use App\Models\Registrar;

class RegistrarRepo
{
    protected $registrars;

    public function __construct(Registrar $registrar )
    {
        $this->registrars = $registrar;
    }

    public function insertRow($data){
        return $this->registrars->create($data);
    }

    public function getAll(){
        return $this->registrars->all();
    }
    public function getAllActive(){
        return $this->registrars->where('status',1)->get();
    }

    public function fetch($id){
        return $this->registrars->find($id);
    }

    public function updateAccount ( $id , $accountData ) {
        return $this->registrars->find($id)->update($accountData);
    }

    public function toggleRow($id, $direction){
        return $this->registrars->find($id)->update(['status'=> $direction]);
    }

    public function getModel(){
        return $this->registrars;
    }

    public function canBeDeleted($id){
        return $this->registrars->find($id)->canModelBeDeleted();
    }

    public function delete($id){
        return $this->registrars->destroy($id);
    }

}