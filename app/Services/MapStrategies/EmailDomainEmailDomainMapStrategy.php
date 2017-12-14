<?php

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;

class EmailDomainEmailDomainMapStrategy implements IMapStrategy {
    public function map($record) {
        return [
            'domain_group_id' => 0 ,
            'domain_name' => $record[ 'domain_name' ] ,
            'is_suppressed' => $record[ 'suppressed' ]
        ];
    }
}
