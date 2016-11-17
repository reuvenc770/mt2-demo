<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services\MapStrategies;

use App\Services\Interfaces\IMapStrategy;
use App\Models\SuppressionReason;

class SuppressListOrangeSuppressionGlobalOrangeMapStrategy implements IMapStrategy {

    public function map($record) {
        $typeId = 0;
        $reasonId = 0;

        $reasons = SuppressionReason::where( 'legacy_status' , $record[ 'suppressionReasonDetails' ] );

        if ( $reasons->count() > 0 ) {
            $reason = $reasons->first();
            $typeId = $reason->suppression_type;
            $reasonId = $reason->id;
        }

        return [
            'email_address' => $record['email_addr'],
            'suppress_datetime' => $record['dateTimeSuppressed'],
            'reason_id' => $reasonId ,
            'type_id' => $typeId
        ];
    }
}
