<?php

namespace App\Repositories;

use App\Models\From;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class FromRepo {
  
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
            ->leftJoin(DB::raw("(SELECT from_id, MAX(send_date) as send_date FROM deploys where offer_id = $offerId GROUP BY from_id) d"), function($join) {
                $join->on('froms.id', '=', 'd.from_id');
            })
            ->where('ofm.offer_id', $offerId)
            ->where('froms.status', 'A')
            ->where('froms.is_approved', 1)
            ->groupBy('forate.from_id', 'name')
            ->orderBy("open_rate", 'desc')
            ->select(DB::raw("froms.id, 
                froms.from_line as name,
                IFNULL(DATEDIFF(curdate(), d.send_date), 10) as days_ago, 
                ROUND(SUM(IFNULL(opens, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS open_rate"))
            ->get();
    }
}