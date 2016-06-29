<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\EmailClientAssignment;
use App\Models\EmailClientAssignmentHistory;

class EmailClientAssignmentRepo {
    protected $assignment;
    protected $history;

    public function __construct ( EmailClientAssignment $assignment , EmailClientAssignmentHistory $history ) {
        $this->assignment = $assignment;
        $this->history = $history;
    }

    public function assignClient ( $emailId , $clientId ) {
        #find current assignment
        
        #if exists record the swap and update
        #else create new assignment
    }

    protected function recordSwap ( $emailId , $prevClientId , $newClientId ) {
        #save inputs to table
    }
}
