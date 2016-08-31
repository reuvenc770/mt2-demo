<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionAssignedRecord;

class AttributionAssignedRecordRepo {
    protected $records;

    public function __construct ( AttributionAssignedRecord $records ) {
        $this->records = $records;
    }

    public function getByFeedId ( $feedId , $daysBack ) {
        #returns stats for given feed ID
    }

    public function getByDeployId ( $deployId , $daysBack ) {
        #returns stats for given deploy ID
    }

    public function getByDaysBack ( $daysBack ) {
        #returns stats for given days back
    }
}
