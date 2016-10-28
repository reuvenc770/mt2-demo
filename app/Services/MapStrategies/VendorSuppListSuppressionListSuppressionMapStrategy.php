<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class VendorSuppListSuppressionListSuppressionMapStrategy implements IMapStrategy {

    public function map($record) {
        return [
            'id' => $record['vendorSuppressionListID'],
            'suppression_list_id' => $record['list_id'],
            'email_address' => $record['email_addr'],
            'upper_case_md5' => md5(strtoupper($record['email_addr'])),
            'lower_case_md5' => md5(strtolower($record['email_addr'])),
        ];
    }
}