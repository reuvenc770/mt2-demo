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
        $tableName = $this->assignment->getTable();

        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                {$tableName} ( email_id , feed_id , capture_date , created_at ,updated_at )
            VALUES
                ( '{$emailId}' , '{$feedId}' , '{$captureDate}' , NOW() , NOW() )
            ON DUPLICATE KEY UPDATE
                feed_id = VALUES( feed_id ) ,
                capture_date = VALUES( capture_date ) ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }

    public function getAssignedFeed ( $emailId , $modelId = null ) {
        $feedId = $this->assignment->where( 'email_id' , $emailId )->pluck( 'feed_id' )->pop();

        if ( is_null( $feedId ) && !is_null( $modelId ) ) {
            $this->assignment->setLiveTable();

            $feedId = $this->assignment->where( 'email_id' , $emailId )->pluck( 'feed_id' )->pop();

            $this->assignment->setModelTable( $modelId );
        }
       
        if ( is_null( $feedId ) ) {
            return 0;
        }

        return $feedId;
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
