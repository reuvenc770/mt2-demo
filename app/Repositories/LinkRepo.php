<?php

namespace App\Repositories;

use App\Models\Link;

class LinkRepo {
    
    private $model;

    public function __construct(Link $model) {
        $this->model = $model;
    }

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

    public function getLinkId($url) {
        return $this->model->firstOrCreate(['url' => $url], ['url' => $url])->id;
    }
}