<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AdvertiserSubjectSubjectMapStrategy implements IMapStrategy {
    
    public function mapList($records) {
        return array_map([$this, 'map'], $records);
    }

    protected function map($record) {
        return [
            'id' => $record['subject_id'],
            'from_line' => $record['advertiser_subject'],
            'approved' => $record['approved_flag'],
            'status' => $record['status'],
        ];
    }
}