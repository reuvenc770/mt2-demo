<?php

namespace App\Repositories;

use App\Models\Creative;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
class CreativeRepo {
  
    private $model;

    public function __construct(Creative $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }

    public function prepareTableForSync() {}

    public function getCreativeOfferClickRate($offerId) {
        $schema = config("database.connections.reporting_data.database");
        return $this->model
            ->leftJoin("$schema.offer_creative_maps as ocm", 'creatives.id', '=', 'ocm.creative_id')
            ->leftJoin("$schema.creative_clickthrough_rates as crate", 'crate.creative_id', '=', 'creatives.id')
            ->leftJoin(DB::raw("(SELECT creative_id, MAX(send_date) as send_date FROM deploys where offer_id = $offerId GROUP BY creative_id) d"), function($join) {
                $join->on('creatives.id', '=', 'd.creative_id');
            })
            ->where('ocm.offer_id', $offerId)
            ->where('creatives.status', 'A')
            ->where('creatives.is_approved', 1)
            ->groupBy('creatives.id', 'name')
            ->orderBy("click_rate", 'desc')
            ->select(DB::raw("creatives.id, 
                creatives.file_name as name, 
                IFNULL(DATEDIFF(curdate(), d.send_date), 10) as days_ago,
                ROUND(SUM(IFNULL(clicks, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS click_rate"))
            ->get();

            # Default send date of 10 days ago just to avoid PHP casting issues
    }

    public function getCreativesByOffer($offerId)
    {
        $schema = config("database.connections.reporting_data.database");
        return $this->model//LAME
            ->leftJoin("$schema.offer_creative_maps as ocm", 'creatives.id', '=', 'ocm.creative_id')
            ->where('ocm.offer_id', $offerId)
            ->where('creatives.status', 'A')
            ->where('creatives.is_approved', 1)
            ->get();
    }

}
