<?php

namespace App\Repositories;

use App\Models\Link;

class LinkRepo {
    
    private $model;

    public function __construct(Link $model) {
        $this->model = $model;
    }

    
}