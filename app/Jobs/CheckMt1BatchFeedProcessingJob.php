<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use App\Models\MT1Models\User as Feeds;
use Maknz\Slack\Facades\Slack;
use App\Models\ProcessedFeedFile;

class CheckMt1BatchFeedProcessingJob extends MonitoredJob {
    const SLACK_CHANNEL = '#cmp_hard_start_errors';

    protected $jobName = "CheckMt1BatchFeedProcessingJob";
    protected $tracking;

    public function __construct ( $tracking , $runtimeThreshold="15m" ) {
        $this->tracking = $tracking;
        $this->jobName .= $tracking;

        parent::__construct(
            $this->jobName , 
            $runtimeThreshold ,
            $tracking
        );
    }

    public function handleJob () {
        $this->remote = \App::make( \App\Services\RemoteLinuxSystemService::class );

        $this->remote->initSshConnection(
            config('ssh.servers.cmpte_feed_file_server.host'),
            config('ssh.servers.cmpte_feed_file_server.port'),
            config('ssh.servers.cmpte_feed_file_server.username'),
            config('ssh.servers.cmpte_feed_file_server.public_key'),
            config('ssh.servers.cmpte_feed_file_server.private_key')
        );

        $this->createNewFeedMt1Directories();

        $this->alertForOrangeFiles();

        $this->moveRedFiles();
    }

    protected function createNewFeedMt1Directories () {
        $homeDirectoryNames = Feeds::where( 'status' , 'A' )->pluck( 'username' )->toArray();

        foreach ( $homeDirectoryNames as $currentFeedName ) {
            if ( !$this->remote->directoryExists( '/home/mt1/' . $currentFeedName ) ) {
                $this->remote->createDirectory( '/home/mt1/' . $currentFeedName );
            }
        }
    }

    protected function alertForOrangeFiles () {
        $homeDirectoryNames = Feeds::where( [ [ 'status' , '=' , 'A'] , [ 'OrangeClient' , '=' , 'Y' ] ] )->pluck( 'username' )->toArray();

        $findOptions = [
            '-type f' ,
            '-mtime -1' ,
            '-mmin +10' , #older than 10 minutes
            '-mmin -120' , #up to 2 hours old
            ' -not -path "/home/mt1/*"' ,
            "\( -name '*.csv' -o -name '*.txt' \)" ,
            '-print' 
        ];

        foreach ( $homeDirectoryNames as $currentFeedName ) {
            if ( !$this->remote->directoryExists( '/home/' . $currentFeedName ) ) {
                continue;
            }

            if ( $newFileString = $this->remote->getRecentFiles( '/home/' . $currentFeedName , $findOptions ) ) {
                Slack::to( self::SLACK_CHANNEL )->send( "Found Orange Feed Files which were not migrated to MT1 folders. File List:\n" . $newFileString ); 
/*
                foreach ( explode( "\n" , $newFileString ) as $orangeFile ) {
                    if ( $orangeFile !== '' ) {
                        if ( !$this->remote->directoryExists( '/home/mt1/' . $currentFeedName ) ) {
                            $this->remote->createDirectory( '/home/mt1/' . $currentFeedName );
                        }

                        \Log::info( 'Moving orange file ' . $orangeFile );
                        $newPath = '/home/mt1' . str_replace( '/home' , '' , $orangeFile ); 
                        $output = $this->remote->moveFile( $orangeFile , $newPath );
                    }
                }
*/
            }
        }
    }

    protected function checkForNoDataStream () {
        if ( ProcessedFeedFile::whereRaw( 'created_at >= NOW() - interval 2 HOUR' )->count() == 0 ) {
            Slack::to( self::SLACK_CHANNEL )->send( "No Record Data coming in...please investigate!" ); 
        }
    }

    protected function moveRedFiles () {
        $homeDirectoryNames = Feeds::where( [ [ 'status' , '=' , 'A'] , [ 'OrangeClient' , '=' , 'N' ] ] )->pluck( 'username' )->toArray();

        $findOptions = [
            '-type f' ,
            '-mtime -1' ,
            ' -not -path "/home/mt1/*"' ,
            "\( -name '*.csv' -o -name '*.txt' \)" ,
            '-print' 
        ];

        foreach ( $homeDirectoryNames as $currentFeedName ) {
            if ( !$this->remote->directoryExists( '/home/' . $currentFeedName ) ) {
                continue;
            }

            if ( $newFileString = $this->remote->getRecentFiles( '/home/' . $currentFeedName , $findOptions ) ) {
                foreach ( explode( "\n" , $newFileString ) as $redFile ) {
                    if ( $redFile !== '' ) {
                        \Log::debug( 'Moving red file ' . $redFile );
                        $newPath = '/home/mt1' . str_replace( '/home' , '' , $redFile ); 
                        $output = $this->remote->moveFile( $redFile , $newPath );
                    }
                }
            }
        }
    }
}
