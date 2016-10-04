<?php

namespace App\Repositories;

use App\Models\CakeVertical;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;

class CakeVerticalRepo {
  
    private $offer;

    public function __construct(CakeVertical $offer) {
        $this->offer = $offer;
    }

    public function updateOrCreate($data) {
        $this->offer->updateOrCreate(['id' => $data['id']], $data);
    }

}