<?php

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\EspRepo;

class EspService
{
    protected $espRepo;

    public function __construct(EspRepo $espRepo)
    {
        $this->espRepo = $espRepo;
    }

    public function getAllEsps(){
        return $this->espRepo->getEspIdAndName();
    }
}