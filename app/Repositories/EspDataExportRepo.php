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

    public function getAll() {
        return $this->model->where('status', 1)->get();
    }

    public function getForEspAccountId($espAccountId) {
        return $this->model->where('esp_account_id', $espAccountId)->get();
    }
}