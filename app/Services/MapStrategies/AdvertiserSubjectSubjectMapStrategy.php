<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AdvertiserSubjectSubjectMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['subject_id'],
            'subject_line' => $record['advertiser_subject'],
            'is_approved' => ($record['approved_flag'] === 'Y' ? 1 : 0),
            'status' => ($record['status'] === 'A' ? 1 : 0),
            'is_original' => ($record['original_flag'] === 'Y' ? 1 : 0),
            'date_approved' => $record['date_approved'],
            'approved_by' => $record['approved_by'],
            'inactive_date' => $record['inactive_date'],
            'internal_approved_flag' => ($record['internal_approved_flag'] === 'Y' ? 1 : 0),
            'internal_date_approved' => $record['internal_date_approved'],
            'internal_approved_by' => $record['internal_approved_by'],
            'copywriter' => ($record['copywriter'] == 'Y' ? 1 : 0),
            'copywriter_name' => $record['copywriter_name'],
        ];
    }
}