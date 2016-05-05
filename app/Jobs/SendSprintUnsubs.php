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

    protected $ftpCleanup = 0;

    protected $filesProcessed = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $lookBack , $dayLimit = 1 , $tracking , $ftpCleanup = 0 )
    {
        $timezone = config('app.timezone' );

        $this->startOfDay = Carbon::now( $timezone )->subDay( $lookBack )->startOfDay();

        if ( $dayLimit > 1 ) {
            $this->endOfDay = Carbon::now( $timezone )->subDay( $lookBack )->addDay( $dayLimit - 1 )->endOfDay();
        } else {
            $this->endOfDay = Carbon::now( $timezone )->subDay( $lookBack )->endOfDay();
        }

        if ( $ftpCleanup != 1 ) {
            echo "\nStart: {$this->startOfDay}\n";
            echo "\nEnd: {$this->endOfDay}\n";
        }

        $this->tracking = $tracking;

        $fileDate = Carbon::parse( $this->endOfDay )->format( self::FILE_DATE_FORMAT );
        $this->dneFileName = self::FILE_NAME_FORMAT . $fileDate . '.txt'; 
        $this->dneCountFileName = self::FILE_NAME_FORMAT . $fileDate . '.cnt'; 

        $this->ftpCleanup = $ftpCleanup;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->ftpCleanup == 1 ) {
            $this->cleanupFtpAccount();
        } else {
            $this->createUnsubFile();
        }
    }

    protected function cleanupFtpAccount () {
        JobTracking::startEspJob( "Sprint Unsub Job - Cleanup" , '' , '' , $this->tracking , 0 );

        $campaignFiles = Storage::disk( 'sprintUnsubCampaignFTP' )->allFiles();

        foreach ( $campaignFiles as $currentFile ) {
            if ( preg_match( '/.csv$/' , $currentFile ) !== 1 ) { continue; }


            $fileNameSections = explode( '_' , $currentFile );
            $fileDate = Carbon::parse( preg_replace( '/\.csv$/' , '' , $fileNameSections[ 1 ] ) );

            if ( $fileNameSections[ 0 ] !== 'SprintUnsubs' ) { continue; }
            if ( $fileDate->isPast() && !$fileDate->isToday() ) {
                Storage::disk( 'sprintUnsubCampaignFTP' )->move( $currentFile , 'processed/' . $currentFile );
            }
        }

        JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking , $this->attempts() );

        return true;
    }

    protected function createUnsubFile () {
        try {
            JobTracking::startEspJob( "Sprint Unsub Job" , '' , '' , $this->tracking , 0 );

            $campaignFiles = Storage::disk( 'sprintUnsubCampaignFTP' )->allFiles();

            if ( count( $campaignFiles ) <= 3 ) {
                Slack::to( self::SLACK_TARGET_SUBJECT )->send("Sprint Unsub Job - No Campaign files today.");

                JobTracking::changeJobState( JobEntry::SUCCESS , $this->tracking , $this->attempts() );

                return true;
            }

            foreach ( $campaignFiles as $currentFile ) {
                if ( preg_match( '/.csv$/' , $currentFile ) !== 1 ) { continue; }

                $fileNameSections = explode( '_' , $currentFile );
                $fileDate = Carbon::parse( preg_replace( '/\.csv$/' , '' , $fileNameSections[ 1 ] ) );

                if ( $fileNameSections[ 0 ] !== 'SprintUnsubs' ) { continue; }
                if ( !$fileDate->isToday() ) { continue; }

                $lines = explode( PHP_EOL , Storage::disk( 'sprintUnsubCampaignFTP' )->get( $currentFile ) );

                if ( empty( $lines ) ) {
                    Slack::to( self::SLACK_TARGET_SUBJECT )->send("Sprint Unsub Job - File '{$currentFile}' is empty.");

                    continue;
                }

                foreach ( $lines as $campaignName ) {
                    $campaignName = trim( $campaignName );
              
                    if ( empty( $campaignName ) ) { continue; }

                    $campaignDetails = explode( '_' , $campaignName );

                    $espDetails = $this->getEspDetails( $campaignDetails[ 1 ] );

                    if ( is_null( $espDetails ) ) { continue; }

                    $deployId = $this->getDeployId( $campaignName , $campaignDetails , $espDetails );
                    $unsubs = $this->getUnsubs( $deployId , $espDetails[ 'accountId' ] );

                    foreach ( $unsubs as $currentUnsub ) {
                        $unsubEmail = $this->getEmail( $currentUnsub->email_id );

                        $this->appendEmailToFile( $unsubEmail , $currentUnsub->datetime );
                    }

                    $orphans = $this->getOrphans( $deployId , $espDetails[ 'accountId' ] );

                    foreach ( $orphans as $currentOrphan ) {
                        $this->appendEmailToFile( $currentOrphan->email_address , $currentOrphan->datetime );
                    }
                }

                $this->filesProcessed++;
            }

            if ( $this->filesProcessed === 0 ) {
                Slack::to( self::SLACK_TARGET_SUBJECT )->send("Sprint Unsub Job - No Campaign files today.");
            }

            if ( $this->unsubCount === 0 ) {
                Slack::to( self::SLACK_TARGET_SUBJECT )->send( "Sprint Unsub Job - No Unsubs Found." );
            } else {
                Storage::append( self::DNE_FOLDER . $this->dneFileName , chr( 10 ) . chr( 13 ) );
            }

            echo "\n\nProcessed '{$this->unsubCount}' Records....\n\n";

            Storage::put( self::DNE_FOLDER . $this->dneCountFileName , $this->unsubCount );

            Storage::disk( 'sprintUnsubFTP' )->put( $this->dneFileName , Storage::get( self::DNE_FOLDER . $this->dneFileName ) );
            Storage::disk( 'sprintUnsubFTP' )->put( $this->dneCountFileName , Storage::get( self::DNE_FOLDER . $this->dneCountFileName ) );

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

    protected function getDeployId ( $campaignName , $campaignDetails , $espDetails ) {
        $deployId = null;

        if ( $espDetails[ 'name' ] === 'BlueHornet' ) {
            $deployId = $campaignDetails[ 0 ];
        } else {
            $deployId = $this->parseSubID( $campaignName );
        }

        return $deployId;
    } 

    public function parseSubID($deploy_id){
        $return = isset(explode("_", $deploy_id)[0]) ? explode("_", $deploy_id)[0] : "";
        return $return;
    }

    protected function getUnsubs ( $deployId , $accountId ) {
        return EmailAction::where( [
            [ 'deploy_id' , $deployId ] ,
            [ 'esp_account_id' , $accountId ] ,
            [ 'action_id' , self::UNSUB_ACTION_ID ]
        ] )->whereBetween( 'datetime' , [ $this->startOfDay , $this->endOfDay ] )->get();
    }

    protected function getEmail ( $emailId ) {
        $emailRecord = Email::select( 'email_address as email' )->where( 'id' , $emailId )->pluck( 'email' );

        if ( isset( $emailRecord[ 0 ] ) ) return $emailRecord[ 0 ];
        else return '';
    }

    protected function getOrphans ( $deployId , $accountId ) {
        return  OrphanEmail::where( [
            [ 'deploy_id' , $deployId ] ,
            [ 'esp_account_id' , $accountId ] ,
            [ 'action_id' , self::UNSUB_ACTION_ID ]
        ] )->whereBetween( 'datetime' , [ $this->startOfDay , $this->endOfDay ] )->get();
    }

    protected function incrementCount () {
        $this->unsubCount++;
    }

    protected function appendEmailToFile ( $email , $date ) {
        if ( $this->isUniqueEmail( $email ) ) {
            if ( !Storage::exists( self::DNE_FOLDER . $this->dneFileName ) ) {
                Storage::put( self::DNE_FOLDER . $this->dneFileName , sprintf( self::RECORD_FORMAT ,  $email , Carbon::parse( $date )->format( 'm/d/Y H:i:s' ) ) );
            } else {
                Storage::append( self::DNE_FOLDER . $this->dneFileName , sprintf( self::RECORD_FORMAT ,  $email , Carbon::parse( $date )->format( 'm/d/Y H:i:s' ) ) );
            }
            
            $this->appendToFullUnsubList( $email );

            $this->incrementCount();
        }
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
