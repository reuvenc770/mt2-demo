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
        $this->assignment->updateOrCreate(['email_id' => $email_id], [
            'client_id' => $clientId,
            'capture_date' => $captureDate
        ]);
    }

    public function getAssignedClient ( $emailId , $modelId = null ) {
        return $this->assignment->where( 'email_id' , $emailId )->pluck( 'client_id' )->pop();
    }

    protected function recordSwap ( $emailId , $prevClientId , $newClientId ) {
        $this->history->create([
            'email_id' => $emailId,
            'prev_client_id' => $prevClientId,
            'new_client_id' => $newClientId
        ]);
    }
}
