<?php

namespace App\Repositories;

use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class SubjectRepo extends AbstractDataSyncRepo{
  
    private $model;

    public function __construct(Subject $model) {
        $this->model = $model;
    } 

    public function updateOrCreate($data) {
        $this->model->updateOrCreate(['id' => $data['id']], $data);
    }


    public function getSubjectOfferOpenRate($offerId) {
        $schema = config("database.connections.reporting_data.database");
        return $this->model
            ->leftjoin("$schema.offer_subject_maps as osm", 'subjects.id', '=', 'osm.subject_id')
            ->leftjoin("$schema.subject_open_rates as sorate", 'sorate.subject_id', '=', 'subjects.id')
            ->where('osm.offer_id', $offerId)
            ->where('subjects.status', 'A')
            ->where('subjects.is_approved', 1)
            ->groupBy('subjects.id', 'name')
            ->orderBy("open_rate", 'desc')
            ->select(DB::raw("subjects.id, subjects.subject_line as name, ROUND(SUM(IFNULL(opens, 0)) / SUM(IFNULL(delivers, 0)) * 100, 3) AS `open_rate`"))
            ->get();
    }

    public function bulkInsert()
    {
        //Interface Adherence and maybe update later
    }

}