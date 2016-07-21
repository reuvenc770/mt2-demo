<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 2:01 PM
 */

namespace App\Services;


use App\Repositories\RegistrarRepo;
use Log;
class RegistrarService
{

    protected $registrar;

    public function __construct(RegistrarRepo $registrarRepo)
    {
        $this->registrar = $registrarRepo;
    }
    public function insertRow($request){
        try {
            return $this->registrar->insertRow($request);
        } catch(\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAll(){
        return $this->registrar->getAll();
    }

    public function getRegistrar($id){
        return $this->registrar->fetch($id);
    }

    public function updateAccount($id, $accountData){
        return $this->registrar->updateAccount( $id , $accountData );
    }
}