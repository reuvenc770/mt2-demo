<?php

namespace App\Jobs;

use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use DB;

use App\Models\Email;
use App\Models\EmailClientInstance;
use App\Models\OrphanEmail;

class AdoptOrphanEmails extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $orphans;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $orphans = [] )
    {
        $this->orphans = is_array( $orphans ) ? collect( $orphans ) : $orphans;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info( '' );
        Log::info( 'Attempting to save some orphans and feed them.' );

        $attempts = 0;
        $processed = 0;

        $this->orphans->each( function ( $item , $key ) use ( &$attempts , &$processed ) {
            $currentOrphan = OrphanEmail::find( $item->id );
            $currentEmailId = 0;
            $currentClientId = 0;
            $failedToProcess = false;

            $emailRecordCount = Email::where( 'email_address' , $item->email_address )->count();
            if ( $emailRecordCount > 0 ) {
                $currentEmailId = Email::select( 'id' )->where( 'email_address' , $item->email_address )->pluck( 'id' )->first();

                $clientRecordCount = EmailClientInstance::where( 'email_id' , $currentEmailId )->count();
                if ( $clientRecordCount > 0 ) {
                    $currentClientId = EmailClientInstance::select( 'client_id' )->where( 'email_id' , $currentEmailId )->pluck( 'client_id' )->first();

                    DB::connection( 'reporting_data' )->statement("
                        INSERT INTO email_actions
                            ( email_id , client_id , esp_account_id , campaign_id , action_id , datetime , created_at , updated_at )    
                        VALUES
                            ( ? , ? , ? , ? , ? , ? , NOW() , NOW() )
                        ON DUPLICATE KEY UPDATE
                            email_id = email_id ,
                            client_id = client_id ,
                            esp_account_id = esp_account_id ,
                            campaign_id = campaign_id ,
                            action_id = action_id ,
                            datetime = datetime ,
                            created_at = created_at ,
                            updated_at = NOW()" ,
                        [
                            $currentEmailId ,
                            $currentClientId ,
                            $currentOrphan->esp_account_id ,
                            $currentOrphan->campaign_id ,
                            $currentOrphan->action_id ,
                            $currentOrphan->datetime
                        ]
                    );

                    $currentOrphan->delete();

                    $processed++;
                } else {
                    $failedToProcess = true;

                    $attempts++;
                }
            } else {
                $failedToProcess = true;

                $attempts++;
            }

            if ( $failedToProcess ) {
                $currentOrphan->increment( 'adopt_attempts' );
            }
        } );

        Log::info( 'Successfully Processed ' . $processed . ' Orphan Emails.' );
        Log::info( 'Failed Processing ' . $attempts . ' Orphan Emails.' );
    }
}
