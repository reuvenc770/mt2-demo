<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\Link;
use App\Models\MT1Models\LiveLink;
use DB;
use Carbon\Carbon;
use App\Repositories\RepoInterfaces\Mt1Import;

class LinkRepo implements Mt1Import {
    protected $model;
    private $liveModel;

    public function __construct ( Link $model, LiveLink $liveModel ) {
        $this->model = $model;
        $this->liveModel = $liveModel;
    }

    public function pullForSync($lookback) {
        $minId = $this->model
                    ->selectRaw('min(link_id) as min')
                    ->whereRaw('date_added >= curdate() - interval 5 day')->first()->min;
        
        return $this->model
                    ->whereRaw("link_id >= $minId");
    }

    public function updateOrCreate($data) {
        $mappedData = [
            'link_id' => $data['id'],
            'refurl' => $data['url'],
            'date_added' => Carbon::now()->toDateTimeString()
        ];

        $this->liveModel->updateOrCreate(['link_id' => $mappedData['link_id']], $mappedData);
    }

    public function getLinkId($url) {
        return $this->liveModel->firstOrCreate(['refurl' => $url], [
            'refurl' => $url, 
            'date_added' => Carbon::now()->toDateTimeString()
        ])->link_id;
    }

    public function insertToMt1($data) {
        $this->liveModel->updateOrCreate(['link_id' => $data['link_id']], $data);
    }
}