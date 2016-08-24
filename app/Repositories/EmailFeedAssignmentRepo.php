<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

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

    public function setLevelModel ( $modelId ) {
        $this->assignment->setModelTable( $modelId );
    }

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( EmailFeedAssignment::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->bigInteger( 'email_id' )->unsigned();
            $table->integer( 'feed_id' )->unsigned();
            $table->date('capture_date');
            $table->timestamps();

            $table->primary( 'email_id' );
            $table->index( 'feed_id' );
            $table->index( [ 'email_id' , 'feed_id' ] );
        });
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( EmailFeedAssignment::BASE_TABLE_NAME . $modelId );
    }
}
