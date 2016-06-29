<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionRecordTruth;

class AttributionRecordTruthRepo {
    protected $truths;

    public function __construct ( AttributionRecordTruth $truths ) {
        $this->truths = $truths;
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

    public function setRecordExpired ( $emailId ) {
        #sets email record to expired
    }

    public function isRecordExpired ( $emailId ) {
        #returns expiration status of email record
    }

    public function toggleRecordActivity ( $emailId , $active = true ) {
        #sets the email record to active or inactive
    }

    public function isRecordActive ( $emailId ) {
        #returns active status of email record
    }
}
