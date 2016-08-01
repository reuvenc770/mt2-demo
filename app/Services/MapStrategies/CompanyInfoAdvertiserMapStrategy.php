<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CompanyInfoAdvertiserMapStrategy implements IMapStrategy {
    
    public function mapList($records) {
        return array_map([$this, 'map'], $records);
    }

    protected function map($record) {
        return [
            'id' => $record['company_id'],
            'name' => $record['company_name']
        ];
    }
}