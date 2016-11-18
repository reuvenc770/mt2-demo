<?php

namespace App\Repositories;

use App\Models\EtlPickup;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class EtlPickupRepo {
    
    private $etlPickup;

    public function __construct(EtlPickup $etlPickup) {
        $this->etlPickup = $etlPickup;
    }

    public function getLastInsertedForName($etlName) {
        $result = $this->etlPickup
                    ->select('stop_point')
                    ->where('name', '=', $etlName)
                    ->get();

        if (isset($result[0]) && isset($result[0]['stop_point'])) {
            return $result[0]['stop_point'];
        }

        throw new \Exception("No stop point listed for $etlName");
    }

    public function updatePosition($etlName, $pos) {
        $this->etlPickup->where('name', $etlName)->update(['stop_point' => $pos]);
    }

    public function updateOrCreate($tableName, $value) {
        $this->etlPickup->updateOrCreate(['name' => $tableName], [
            'name' => $tableName,
            'stop_point' => $value
        ]);
    }
}