<?php

namespace App\Repositories;
use App\Models\EspDataExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class EspDataExportRepo {
    private $model;

    public function __construct(EspDataExport $model) {
        $this->model = $model;
    }

    public function getForFeedId($feedId) {
        return $this->model->where('feed_id', $feedId)->get();
    }
}