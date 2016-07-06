<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionRecordTruth;

class AttributionRecordTruthRepo {

    protected $truth;

    public function __construct ( AttributionRecordTruth $truth ) {
        $this->truth = $truth;
    }

    public function getAssignedRecords () {
        #queries table for records that are not transient.
    }

    public function getTransientRecords () {
        #queries table and finds expired:true|active:false combos and returns their email IDs
    }

    public function resetRecord () {
        #resets the record to initial value => expired:false|active:false
    }

    public function setField($emailId, $field, $value){
        return $this->truth->where("email_id", $emailId)->update(array($field =>$value));
    }
}
