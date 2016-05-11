<?php

namespace App\Jobs;

use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use DB;
use App\Facades\JobTracking;

use App\Models\Email;
use App\Models\EmailClientInstance;
use App\Models\OrphanEmail;
use App\Models\JobEntry;

class AdoptOrphanEmails extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $orphans;
    protected $tracking;
    protected $firstId;
    protected $lastId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $orphans = [] , $firstId = 0 , $lastId = 0 )
    {
        $this->orphans = is_array( $orphans ) ? collect( $orphans ) : $orphans;

        $this->tracking = str_random( 16 );

        $this->firstId = $firstId;
        $this->lastId = $lastId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        $this->initJobEntry();
        Log::info( '' );
        Log::info( 'Attempting to save some orphans and feed them.' );

        $attempts = 0;
        $processed = 0;

        $inserts = [];
        $deleteIds = [];

        foreach ($this->orphans as $orphan) {
            $orphan = OrphanEmail::find( $orphan->id );
            $currentEmailId = 0;
            $currentClientId = 0;
            $failedToProcess = false;

            if ( is_object( $orphan ) ) {
                $emailRecordCount = Email::where( 'email_address' , $orphan->email_address )->count();
                if ( $emailRecordCount > 0 ) {
                    $currentEmailId = Email::select( 'id' )->where( 'email_address' , $orphan->email_address )->pluck( 'id' )->first();

                    $clientRecordCount = EmailClientInstance::where( 'email_id' , $currentEmailId )->count();
                    if ( $clientRecordCount > 0 ) {
                        $currentClientId = EmailClientInstance::select( 'client_id' )->where( 'email_id' , $currentEmailId )->pluck( 'client_id' )->first();
                    }
                    else {
                        $currentClientId = 0;
                    }

                    $value = "('$currentEmailId', '$currentClientId', 
                        '{$orphan->esp_account_id}', 
                        '{$orphan->deploy_id}', 
                        '{$orphan->esp_internal_id}', 
                        '{$orphan->action_id}', 
                        '{$orphan->datetime}', NOW(), NOW())";
                    $inserts[]= $value;

                    $deleteIds[] = $orphan->id;
                    $processed++;
                } 
                else {
                    $failedToProcess = true;
                    $attempts++;
                }
            }

            if ( $failedToProcess ) {
                $orphan->increment( 'adopt_attempts' );
            }
        }


        try {
            if (sizeof($inserts) > 0) {
                $insertString = implode(',', $inserts);
                DB::connection( 'reporting_data' )->statement("
                    INSERT INTO email_actions
                        ( email_id , client_id , esp_account_id , deploy_id, esp_internal_id , action_id , datetime , created_at , updated_at )    
                        VALUES
                        $insertString
                        ON DUPLICATE KEY UPDATE
                        email_id = email_id ,
                        client_id = client_id ,
                        esp_account_id = esp_account_id ,
                        deploy_id = deploy_id,
                        esp_internal_id = esp_internal_id ,
                        action_id = action_id ,
                        datetime = datetime ,
                        created_at = created_at ,
                        updated_at = NOW()" 
                );

                DB::table('orphan_emails')
                    ->whereIn('id', $deleteIds)
                    ->delete();
            }

        }
        catch ( Exception $e ) {
            Log::error( 'Query errors:' );
            Log::error( $e->getMessage() );
            Log::error( $e->getTraceAsString() );

            $failedToProcess = true;
            $attempts++;
        }

        Log::info( 'Successfully Processed ' . $processed . ' Orphan Emails.' );
        Log::info( 'Failed Processing ' . $attempts . ' Orphan Emails.' );

        $this->changeJobEntry( JobEntry::SUCCESS );
    }

    protected function initJobEntry () {
        JobTracking::startEspJob( 'Orphan Adoption: ' . $this->firstId . '-' . $this->lastId , null , null , $this->tracking );
    }

    protected function changeJobEntry ( $status ) {
        JobTracking::changeJobState( $status , $this->tracking , $this->attempts() );
    }

    public function failed() {
        $this->changeJobEntry( JobEntry::FAILED );
    }
}
