<?php

namespace App\Repositories;

use App\Models\OrphanEmail;

class OrphanEmailRepo {
    private $model;

    public function __construct(OrphanEmail $model) {
        $this->model = $model;
    }

    public function getMaxId() {
        return $this->model->max('id');
    }

    public function getMinId() {
        return $this->model->min('id');
    }

    public function nextNRows($startId, $count) {
        return $this->model
            ->where('id', '>=', $startId)
            ->orderBy('id')
            ->skip($count)
            ->first()['id'];
    }

    public function getOrphansBetweenIds($startId, $endId) {
        $startId = (int)$startId;
        $endId = (int)$endId;

        if ($startId > 0 && $endId > 0 && $startId <= $endId) {
            return $this->model
                        ->whereRaw("id BETWEEN $startId AND $endId")
                        ->get()
                        ->toArray();
        }
        else {
            return null;
        }
    }

}