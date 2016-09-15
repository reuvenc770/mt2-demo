<?php

namespace App\Services;

use App\Repositories\EspApiRepo;
use League\Csv\Reader;
/**
 * Class EspApiService
 * @package App\Services
 */
class EspService
{
    /**
     * @var EspRepo
     */
    protected $espRepo;

    /**
     * EspService constructor.
     * @param EspApiRepo $espRepo
     */
    public function __construct(EspApiRepo $espRepo)
    {
        $this->espRepo = $espRepo;
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
}
