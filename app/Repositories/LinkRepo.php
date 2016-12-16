<?php

namespace App\Repositories;

use App\Models\Link;
use App\Repositories\RepoInterfaces\Mt2Export;

class LinkRepo implements Mt2Export {
    
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

    public function transformForMt1($startId) {
        return $this->model
                    ->selectRaw('id as link_id, url as refurl, created_at as date_added')
                    ->whereRaw("id > $startId");
    }
}