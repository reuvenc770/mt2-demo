<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use Carbon\Carbon;
use Storage;
use DB;
use App\Models\EspAccount;
use App\Models\EmailAction;
use App\Models\Email;
use App\Models\OrphanEmail;
use Maknz\Slack\Facades\Slack;
use Log;

class SendSprintUnsubs extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    CONST DNE_FOLDER = 'SprintUnsubCampaignCSV/dneFiles/';

    CONST UNSUB_ACTION_ID = 7;

    CONST RECORD_FORMAT = "A\t%s\t%s\tSPRGPROM\tZETA\tBATCH\tE";

    CONST FILE_NAME_FORMAT = 'Zeta_DNE_';
    CONST FILE_DATE_FORMAT = 'YmdHis';

    CONST SLACK_TARGET_SUBJECT = '#mt2-dev-failed-jobs';

    protected $startOfDay;
    protected $endOfDay;

    protected $tracking;
    protected $unsubCount = 0;

    protected $blueHornetCampaigns = [];
    protected $otherCampaigns = [];

    protected $fullUnsubList = [];

    protected $dneFileName;
    protected $dneCountFileName;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $lookBack , $dayLimit = 1 , $tracking )
    {
        $timezone = config('app.timezone' );

        $this->startOfDay = Carbon::now( $timezone )->subDay( $lookBack )->startOfDay();

        if ( $dayLimit > 1 ) {
            $this->endOfDay = Carbon::now( $timezone )->subDay( $lookBack )->addDay( $dayLimit - 1 )->endOfDay();
        } else {
            $this->endOfDay = Carbon::now( $timezone )->subDay( $lookBack )->endOfDay();
        }

        echo "\nStart: {$this->startOfDay}\n";
        echo "\nEnd: {$this->endOfDay}\n";

        $this->tracking = $tracking;

        $fileDate = Carbon::parse( $this->endOfDay )->format( self::FILE_DATE_FORMAT );
        $this->dneFileName = self::FILE_NAME_FORMAT . $fileDate . '.txt'; 
        $this->dneCountFileName = self::FILE_NAME_FORMAT . $fileDate . '.cnt'; 
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            JobTracking::startEspJob( "Sprint Unsub Job" , '' , '' , $this->tracking , 0 );

            $campaignFiles = Storage::disk( 'sprintUnsubCampaignFTP' )->allFiles();

            Storage::put( self::DNE_FOLDER . $this->dneFileName , '' );

            if ( count( $campaignFiles ) > 3 ) {
                foreach ( $campaignFiles as $currentFile ) {

                    if ( preg_match( '/.csv$/' , $currentFile ) ) {
                        $lines = explode( PHP_EOL , Storage::disk( 'sprintUnsubCampaignFTP' )->get( $currentFile ) );

                        if ( !empty( $lines ) ) {
                            foreach ( $lines as $campaignName ) {
                                $campaignName = trim( $campaignName );

                                if ( !empty( $campaignName ) ) {
                                    $campaignDetails = explode( '_' , $campaignName );

                                    $espDetails = $this->getEspDetails( $campaignDetails[ 1 ] );

                                    if ( is_null( $espDetails ) ) continue;

                                    $campaigns = $this->getCampaigns( $espDetails , $campaignName , $campaignDetails );

                                    foreach ( $campaigns as $campaignId ) {
                                        $unsubs = $this->getUnsubs( $campaignId , $espDetails[ 'accountId' ] );

                                        foreach ( $unsubs as $unsubEmailId ) {
                                            $unsubEmail = $this->getEmail( $unsubEmailId );

                                            $this->appendEmailToFile( $unsubEmail );
                                        }

                                        $orphans = $this->getOrphans( $campaignId , $espDetails[ 'accountId' ] );

                                        foreach ( $orphans as $orphanEmail ) {
                                            $this->appendEmailToFile( $orphanEmail );
                                        }
                                    }
                                }
                            }
                        } else {
                            Slack::to( self::SLACK_TARGET_SUBJECT )->send("Sprint Unsub Job - File '{$currentFile}' is empty.");
                        }

                        Storage::disk( 'sprintUnsubCampaignFTP' )->move( $currentFile , 'processed/' . $currentFile );
                    }
                }
            } else {
                Slack::to( self::SLACK_TARGET_SUBJECT )->send("Sprint Unsub Job - No Campaign files today.");
            }

            Storage::put( self::DNE_FOLDER . $this->dneCountFileName , $this->unsubCount );

            Storage::disk( 'sprintUnsubFTP' )->put( $this->dneFileName , Storage::get( self::DNE_FOLDER . $this->dneFileName ) );
            Storage::disk( 'sprintUnsubFTP' )->put( $this->dneCountFileName , Storage::get( self::DNE_FOLDER . $this->dneCountFileName ) );

            Storage::delete( [ self::DNE_FOLDER . $this->dneFileName , self::DNE_FOLDER . $this->dneCountFileName ] );

            JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking , $this->attempts() );

            return true;
        } catch ( \Exception $e ) {
            Log::error( 'Job Failed' );

            throw $e;
        }
    }

    protected function getEspDetails( $shortName ) {
        if ( EspAccount::where( 'account_name' , $shortName )->exists() ) {
            $id = EspAccount::where( 'account_name' , $shortName )->pluck( 'id' );
        } else {
            Slack::to( self::SLACK_TARGET_SUBJECT )->send("Sprint Unsub Job - {$shortName} does not exist in the system.");

            return null;
        }

        $esp = EspAccount::find( $id )->esp;

        return [
            'name' => $esp->name ,
            'accountId' => (int)$id[ 0 ]
        ];
    }

    protected function getCampaigns ( $espDetails , $campaignName , $campaignDetails ) {
        $campaigns = [];

        if ( $espDetails[ 'name' ] === 'BlueHornet' ) {
            $billCode = $campaignDetails[ 0 ];

            $campaigns = DB::connection( 'reporting_data' )
                ->table( 'blue_hornet_reports' )
                ->select( 'internal_id as id' )
                ->where( [
                    [ 'bill_codes' , $billCode ] ,
                    [ 'esp_account_id' , $espDetails[ 'accountId' ] ]
                ] )->whereBetween( 'date_sent' , [ $this->startOfDay , $this->endOfDay ] )->pluck( 'id' );
        } else {
            $tableName = ( $espDetails[ 'name' ] == 'Campaigner' ? 'campaigner_reports' : 'maro_reports' );
            $sentField = ( $espDetails[ 'name' ] == 'Campaigner' ? 'created_at' : 'sent_at' );

            $campaigns = DB::connection( 'reporting_data' )
                ->table( $tableName )
                ->select( 'internal_id as id' )
                ->where( 'name' , $campaignName )
                ->whereBetween( $sentField , [ $this->startOfDay , $this->endOfDay ] )
                ->pluck( 'id' );
        }

        if ( empty( $campaigns ) ) {
            Slack::to( self::SLACK_TARGET_SUBJECT )->send("Sprint Unsub Job - Campaign Name '{$campaignName}' has no matches..");
        }

        return $campaigns;
    }

    protected function getUnsubs ( $campaignId , $accountId ) {
        return EmailAction::select( 'email_id' )->where( [
            [ 'campaign_id' , $campaignId ] ,
            [ 'esp_account_id' , $accountId ] ,
            [ 'action_id' , self::UNSUB_ACTION_ID ]
        ] )->whereBetween( 'datetime' , [ $this->startOfDay , $this->endOfDay ] )->pluck( 'email_id' );
    }

    protected function getEmail ( $emailId ) {
        $emailRecord = Email::select( 'email_address as email' )->where( 'id' , $emailId )->pluck( 'email' );

        if ( isset( $emailRecord[ 0 ] ) ) return $emailRecord[ 0 ];
        else return '';
    }

    protected function getOrphans ( $campaignId , $accountId ) {
        return  OrphanEmail::select( 'email_address as email' )->where( [
            [ 'campaign_id' , $campaignId ] ,
            [ 'esp_account_id' , $accountId ] ,
            [ 'action_id' , self::UNSUB_ACTION_ID ]
        ] )->whereBetween( 'datetime' , [ $this->startOfDay , $this->endOfDay ] )->pluck( 'email' );
    }

    protected function incrementCount () {
        $this->unsubCount++;
    }

    protected function appendEmailToFile ( $email ) {
        if ( $this->isUniqueEmail( $email ) ) {
            Storage::append( self::DNE_FOLDER . $this->dneFileName , sprintf( self::RECORD_FORMAT ,  $email , $this->startOfDay ) );


            $this->appendToFullUnsubList( $email );
        }
            $this->incrementCount();
    }

    protected function isUniqueEmail ( $email ) {
        return !in_array( $email , $this->fullUnsubList );
    }

    protected function appendToFullUnsubList ( $email ) {
        $this->fullUnsubList []= $email;
    }

    public function failed()
    {
        JobTracking::changeJobState( JobEntry::FAILED , $this->tracking , $this->attempts() );

        Slack::to( self::SLACK_TARGET_SUBJECT )->send("Sprint Unsub Job - Failed to run after " . $this->attempts() . " attempts.");
    }
}
