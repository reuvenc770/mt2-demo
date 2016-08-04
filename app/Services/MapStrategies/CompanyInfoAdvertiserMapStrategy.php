<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CompanyInfoAdvertiserMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['company_id'],
            'name' => $record['company_name']
        ];
    }
}