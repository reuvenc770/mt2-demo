<?php

namespace App\Repositories;

use App\Models\Advertiser;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class AdvertiserRepo {
  
    private $advertiser;

    public function __construct(Advertiser $advertiser) {
        $this->advertiser = $advertiser;
    } 

    public function updateOrCreate($data) {
        $this->advertiser->updateOrCreate(['id' => $data['id']], $data);
    }

    public function prepareTableForSync() {}

}