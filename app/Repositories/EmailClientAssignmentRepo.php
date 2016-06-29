<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\EmailClientAssignment;
use App\Models\EmailClientAssignmentHistory;

class EmailClientAssignmentRepo {
    public function __construct () {}

    public function assignClient ( $emailId , $clientId ) {
        $relatedAssignment = EmailClientAssignments::where( 'email_id' , $emailId );

        if ( $relatedAssignment->count() > 0 ) {
            $collection = $relatedAssignment->get();

            foreach ( $collection as $assignment ) {
                $prevClientId = $assignment->client_id;

                $assignment->client_id = $clientId;
                $assignment->save();

                $this->recordSwap( $emailId , $prevClientId , $clientId );
            }
        } else {
            $assignment = new EmailClientAssignment();
            $assignment->email_id = $emailId;
            $assignment->client_id = $clientId;
            $assignment->save();
        }
    }

    protected function recordSwap ( $emailId , $prevClientId , $newClientId ) {
        $swapRecord = new EmailClientAssignmentHistory();

        $swapRecord->email_id = $emailId;
        $swapRecord->prev_client_id = $prevClientId;
        $swapRecord->new_client_id = $newClientId;
        $swapRecord->save();
    }
}
