<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class AdvertiserSubjectSubjectMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['subject_id'],
            'subject_line' => $this->stripWindowsExtendedChars($record['advertiser_subject']),
            'is_approved' => ($record['approved_flag'] === 'Y' ? 1 : 0),
            'status' => $record['status'],
            'is_original' => ($record['original_flag'] === 'Y' ? 1 : 0),
            'date_approved' => $this->fixZeroDate($record['date_approved']),
            'approved_by' => $record['approved_by'],
            'inactive_date' => $this->fixZeroDate($record['inactive_date']),
            'internal_approved_flag' => ($record['internal_approved_flag'] === 'Y' ? 1 : 0),
            'internal_date_approved' => $record['internal_date_approved'],
            'internal_approved_by' => $record['internal_approved_by'],
            'copywriter' => ($record['copywriter'] == 'Y' ? 1 : 0),
            'copywriter_name' => $record['copywriter_name'],
        ];
    }

    private function stripWindowsExtendedChars($str) {
        if (mb_detect_encoding($str) !== false) {
            return $str;
        }
        else {
            // likely some sort of binary string, possibly from windows encoding
            $charArray = unpack('C*', $str);
            $outputArr = [];

            foreach ($charArray as $ord) {
                if ($ord >= 127) {
                    $outputArr[] = mb_convert_encoding(chr($ord), 'utf8', 'cp1252');
                }
                else {
                    $outputArr[] = chr($ord);
                }
            }

            return implode('', $outputArr);
        }
    }

    private function fixZeroDate($date) {
        if ('0000-00-00' === $date) {
            return null;
        }
        else {
            return $date;
        }
    }
}