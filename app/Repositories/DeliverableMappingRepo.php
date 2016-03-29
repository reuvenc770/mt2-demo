<?php

namespace App\Repositories;

use App\Models\DeliverableCsvMapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class DeliverableMappingRepo {

    private $map;

    public function __construct(DeliverableCsvMapping $map) {
        $this->map = $map;
    } 

    public function getMapping($espId) {
        $result = $this->map->select('mapping')->where('esp_id', $espId)->get();
        if (isset($result[0]) && isset($result[0]['mapping'])) {
            return $result[0]['mapping'];
        }
        return '';
    }
}