<?php

namespace App\Repositories;

use App\Models\From;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class FromRepo extends AbstractDataSyncRepo{
  
    private $model;

    public function __construct(From $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

    public function getFromOfferOpenRate($offerId) {
        $schema = config("database.connections.reporting_data.database");
        return $this->model
            ->leftJoin("$schema.offer_from_maps as ofm", 'froms.id', '=', 'ofm.from_id')
            ->leftJoin("$schema.from_open_rates as forate", 'forate.from_id', '=', 'froms.id')
            ->where('ofm.offer_id', $offerId)
            ->where('froms.status', 'A')
            ->where('froms.is_approved', 1)
            ->groupBy('forate.from_id', 'name')
            ->orderBy("open_rate", 'desc')
            ->select(DB::raw("froms.id, froms.from_line as name, ROUND(SUM(IFNULL(opens, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS open_rate"))->get();
    }
    public function bulkInsert()
    {
        //Interface Adherence and maybe update later
    }
}