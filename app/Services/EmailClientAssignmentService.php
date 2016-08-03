<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\EmailClientAssignmentRepo;

class EmailClientAssignmentService {
    protected $repo;

    public function __construct ( EmailClientAssignmentRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAssignedClient ( $emailId , $modelId = null ) {
        return $this->repo->getAssignedClient( $emailId , $modelId );
    }
}
