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
}
