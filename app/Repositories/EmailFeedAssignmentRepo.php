<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\EmailFeedAssignment;
use App\Models\EmailFeedAssignmentHistory;

class EmailFeedAssignmentRepo {
    protected $assignment;
    protected $history;

    public function __construct ( EmailFeedAssignment $assignment , EmailFeedAssignmentHistory $history ) {
        $this->assignment = $assignment;
        $this->history = $history;
    }

    public function assignFeed ( $emailId , $feedId , $captureDate ) {
        $this->assignment->updateOrCreate(['email_id' => $emailId], [
            'feed_id' => $feedId,
            'capture_date' => $captureDate
        ]);
    }

    public function getAssignedFeed ( $emailId , $modelId = null ) {
        return $this->assignment->where( 'email_id' , $emailId )->pluck( 'feed_id' )->pop();
    }

    public function recordSwap ( $emailId , $prevFeedId , $newFeedId ) {
        $this->history->create([
            'email_id' => $emailId,
            'prev_feed_id' => $prevFeedId,
            'new_feed_id' => $newFeedId
        ]);
    }
}
