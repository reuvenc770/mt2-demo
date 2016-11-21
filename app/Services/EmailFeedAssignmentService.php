<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\EmailFeedAssignmentRepo;

class EmailFeedAssignmentService {
    protected $repo;

    public function __construct ( EmailFeedAssignmentRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAssignedFeed ( $emailId , $modelId = null ) {
        return $this->repo->getAssignedFeed( $emailId , $modelId );
    }

    public function setLevelModel ( $modelId ) {
        $this->repo->setLevelModel( $modelId );
    }

    public function insertBulkRecords($records) {
        foreach($records as $record) {
            $this->repo->insertBatch($record);
        }

        $this->repo->insertStored();
    }
}
