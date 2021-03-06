<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/20/16
 * Time: 2:01 PM
 */

namespace App\Services;


use App\Repositories\RegistrarRepo;
use App\Services\ServiceTraits\PaginateList;
use Log;
class RegistrarService
{

    protected $registrar;
    use PaginateList;
    public function __construct(RegistrarRepo $registrarRepo)
    {
        $this->registrar = $registrarRepo;
    }
    public function insertRow($request){
        try {
            $request['dba_names'] = json_encode( $request['dba_names'] );
            return $this->registrar->insertRow($request);
        } catch(\Exception $e){
            Log::error($e->getMessage());
            return false;
        }
    }

    public function getAll(){
        return $this->registrar->getAll();
    }
    public function getAllActive(){
        return $this->registrar->getAllActive();
    }

    public function getRegistrar($id){
        return $this->registrar->fetch($id);
    }

    public function updateAccount($id, $accountData){

        $accountData['dba_names'] = json_encode( $accountData['dba_names'] );
        return $this->registrar->updateAccount( $id , $accountData );
    }

    public function toggleRow($id, $direction){
        return $this->registrar->toggleRow($id, $direction);
    }

    public function getModel(){
        return $this->registrar->getModel();
    }

    //override return model so its a builder and not Collection
    public function getType(){
        return "Registrar";
    }

    public function tryToDelete($id){
        $canBeDeleted =  $this->registrar->canBeDeleted($id);
        if($canBeDeleted === true){
            $this->registrar->delete($id);
            return true;
        } else{
            return $canBeDeleted;
        }
    }
}