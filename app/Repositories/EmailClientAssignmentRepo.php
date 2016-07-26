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

    public function assignClient ( $emailId , $clientId , $captureDate ) {
        $this->assignment->updateOrCreate(['email_id' => $emailId], [
            'client_id' => $clientId,
            'capture_date' => $captureDate
        ]);
    }

    public function recordSwap ( $emailId , $prevClientId , $newClientId ) {
        $this->history->create([
            'email_id' => $emailId,
            'prev_client_id' => $prevClientId,
            'new_client_id' => $newClientId
        ]);
    }
}
