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

    public function getAssignedClient ( $emailId , $modelId = null ) {
        return $this->assignment->where( 'email_id' , $emailId )->pluck( 'client_id' )->pop();
    }

    public function recordSwap ( $emailId , $prevClientId , $newClientId ) {
        $this->history->create([
            'email_id' => $emailId,
            'prev_client_id' => $prevClientId,
            'new_client_id' => $newClientId
        ]);
    }

    public function setLevelModel ( $modelId ) {
        $this->assignment->setModelTable( $modelId );
    }

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( EmailClientAssignment::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->bigInteger( 'email_id' )->unsigned();
            $table->integer( 'client_id' )->unsigned();
            $table->date('capture_date');
            $table->timestamps();

            $table->primary( 'email_id' );
            $table->index( 'client_id' );
            $table->index( [ 'email_id' , 'client_id' ] );
        });
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( EmailClientAssignment::BASE_TABLE_NAME . $modelId );
    }
}
