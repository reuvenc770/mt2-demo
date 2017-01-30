<?php

namespace App\Services;

use App\Repositories\EspRepo;
use App\Services\ServiceTraits\PaginateList;
use Illuminate\Foundation\Bus\DispatchesJobs;
use League\Csv\Reader;
/**
 * Class EspApiService
 * @package App\Services
 */
class EspService
{
    use PaginateList;
    /**
     * @var EspRepo
     */
    protected $espRepo;

    /**
     * EspService constructor.
     * @param EspRepo $espRepo
     */
    public function __construct(EspRepo $espRepo)
    {
        $this->espRepo = $espRepo;
    }

    public function insertRow ( $request ) {
        try {
            $esp = $this->espRepo->insertRow( [ 'name' => $request[ 'name' ] , 'nickname' => strtolower($request['nickname']) ] );

            $this->espRepo->updateFieldOptions( $esp->id , $request );
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return false;
        }
    }
    public function getAccount($id){
        return $this->espRepo->getAccountWithFields($id);
    }

    public function getAccountWithEditCheck($id){
        $data = $this->espRepo->getAccountWithFields($id);
        $data->hasAccounts = $data->espAccounts()->count() != 0;
        return $data;
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getAllEsps () {
        return $this->espRepo->getAllEsps();
    }

    public function getIdByName($name){
        $esp = $this->espRepo->getEspByName($name);
        return $esp->id;
    }
    public function getMappings($id){
        return $this->espRepo->getMappings($id);
    }

    public function updateMappings($mapping,$espId){
        return $this->espRepo->updateEspMappings($mapping,$espId);
    }

    public function getModel(){
        return $this->espRepo->returnModelwithFields();
    }

    public function updateAccount($id, $fieldOptions){
        $this->espRepo->updateEspName($id, $fieldOptions['name'] , strtolower($fieldOptions['nickname']) );
        return $this->espRepo->updateFieldOptions($id, $fieldOptions);
    }

    //override return model so its a builder and not Collection
    public function getType(){
        return "Esp";
    }

}
