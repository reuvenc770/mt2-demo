<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class CreativeCreativeMapStrategy implements IMapStrategy {
    
    public function mapList($records) {
        return array_map([$this, 'map'], $records);
    }

    protected function map($record) {
        return [
            'id' => $record['creative_id'],
            'name' => $record['creative_name'],
            'file_name' => $record[''],
            'approved' => $record['approved_flag'],
            'status' => $record['status'],
            'creative_html' => $record['html_code']
        ];
    }
}