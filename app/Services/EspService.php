<?php

namespace App\Services;

use App\Repositories\EspRepo;
use App\Services\ServiceTraits\PaginateList;
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
            $esp = $this->espRepo->insertRow( [ 'name' => $request[ 'name' ] ] );

            $this->espRepo->updateFieldOptions( $esp->id , $request );
        } catch(\Exception $e){
            \Log::error($e->getMessage());
            return false;
        }
    }
    public function getAccount($id){
        return $this->espRepo->getAccountWithFields($id);
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

    public function updateMappings($mapping){
        return $this->updateEspMappings($mapping);
    }

    public function getModel(){
        return $this->espRepo->returnModelwithFields();
    }

    public function updateAccount($id, $fieldOptions){
        //only field options for now
       return  $this->espRepo->updateFieldOptions($id, $fieldOptions);
    }

    //override return model so its a builder and not Collection
    public function getType(){
        return "Esp";
    }
}
