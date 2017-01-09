<?php

namespace App\Repositories;

use App\Models\Esp;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Log;
use Event;
/**
 * Class EspApiRepo
 * @package App\Repositories
 */
class EspRepo
{
    /**
     * @var Esp
     */
    protected $esp;

    /**
     * EspApiRepo constructor.
     * @param Esp $esp
     */
    public function __construct( Esp $esp )
    {
        DB::enableQueryLog();
        $this->esp = $esp;
    }

    /**
     * @return Illuminate\Database\Eloquent\Collection
     */

    public function insertRow ( $data ) {
        return $this->esp->create( $data );
    }

    public function getAllEsps () {
        return $this->esp->orderBy('name')->get();
    }

    public function getEspByName($name){
        return $this->esp->where('name', $name)->first();
    }

    public function updateEspMappings($mappings,$espId){
      return $this->esp->accountMapping()->getRelated()->updateOrCreate(["esp_id" =>$espId],$mappings);
    }

    public function updateEspName($id, $name){
        return $this->esp->find($id)->update(['name' =>$name]);
    }

    public function returnModelwithFields(){
        $esp = $this->esp;//cannot use $this-> to invoke static method
        return $esp::with('fieldOptions');
    }

    public function getAccountWithFields($id){
        $esp = $this->esp;//cannot use $this-> to invoke static method
        return $esp::with('fieldOptions')->find($id);
    }

    public function getMappings($id){
        return $this->esp->find($id)->accountMapping()->get();
    }

    public function updateFieldOptions($id, $fieldOptions){
       $return =  $this->esp->find($id)->fieldOptions()->updateOrCreate(["esp_id" => $id],
                ["email_address_field" => $fieldOptions['email_address_field'],
                    "email_id_field" => $fieldOptions['email_id_field'],
                    "open_email_id_field" => $fieldOptions['open_email_id_field'],
                    "open_email_address_field" => $fieldOptions['open_email_address_field']
                ]
            );
        /**
         *Have to manually fire the event because ESP is not dirty so there is no update, but the list view
         *is dependant on the ESP model
         **/
        Event::fire('eloquent.updated: App\Models\Esp', $this->esp);

        return $return;
    }
}
