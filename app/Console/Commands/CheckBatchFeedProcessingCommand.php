<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MT1Models\User as Feeds;
use Maknz\Slack\Facades\Slack;
use Carbon\Carbon;
use Notify;

class CheckBatchFeedProcessingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:checkBatchCmpte';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if there are any old files that were not moved for MT1.';

    const SLACK_CHANNEL = '#cmp_hard_start_errors';
    protected $remote;
    protected $feedFileService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->remote = \App::make( \App\Services\RemoteLinuxSystemService::class );
        $this->feedFileService = \App::make( \App\Services\ProcessedFeedFileService::class ); 

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

        /**
         * Switching connection to realtime for other checks
         */
        $this->remote->initSshConnection(
            config('ssh.servers.cmpte_realtime_server.host'),
            config('ssh.servers.cmpte_realtime_server.port'),
            config('ssh.servers.cmpte_realtime_server.username'),
            config('ssh.servers.cmpte_realtime_server.public_key'),
            config('ssh.servers.cmpte_realtime_server.private_key')
        );

        $this->findUnprocessedRealtimeFiles();
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
            '-mmin +45' ,
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

                foreach ( explode( "\n" , $newFileString ) as $orangeFile ) {
                    if ( $orangeFile !== '' ) {
                        \Log::info( 'Moving orange file ' . $orangeFile );
                        $newPath = '/home/mt1' . str_replace( '/home' , '' , $orangeFile ); 
                        $output = $this->remote->moveFile( $orangeFile , $newPath );
                    }
                }
            }
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
                        \Log::info( 'Moving red file ' . $redFile );
                        $newPath = '/home/mt1' . str_replace( '/home' , '' , $redFile ); 
                        $output = $this->remote->moveFile( $redFile , $newPath );
                    }
                }
            }
        }
    }

    protected function findUnprocessedRealtimeFiles () {
        $realtimeDirectory = '/var/local/programdata/done/mt2_realtime/';

        $findOptions = [
            '-type f' ,
            '-mtime -1' ,
            '-printf "%f,%TY-%Tm-%Td %TH:%Tm\n"'
        ];

        if ( $newFileString = $this->remote->getRecentFiles( $realtimeDirectory , $findOptions ) ) {
            $files = [];
            $unprocessedFiles = [];

            foreach ( explode( "\n" , $newFileString ) as $line ) {
                if ( empty( $line ) ) {
                    continue;
                }

                $lineArray = explode( ',' , $line );

                $file = [
                    'path' => $realtimeDirectory . $lineArray[ 0 ] ,
                    'count' => $this->remote->getFileLineCount( $realtimeDirectory . $lineArray[ 0 ] ) ,
                    'lastModified' => Carbon::parse( $lineArray[ 1 ] )
                ];

                $files []= $file;

                if ( !$this->feedFileService->fileWasProcessed( $file[ 'path' ] ) ) {
                    $unprocessedFiles []= array_merge( $file , [ 'reason' => 'Could not find file in processed_feed_files table.' ] );

                    continue;
                }

                if ( !$this->feedFileService->fileLineCountMatches( $file[ 'path' ] , $file[ 'count' ] ) ) {
                    $unprocessedFiles []= array_merge( $file , [ 'reason' => 'Line Count did not match in processed_feed_files table.' ] );

                    continue;
                }

                $timestamp = $this->feedFileService->getProcessedTime( $file[ 'path' ] );
                if ( -60 > Carbon::parse( $file[ 'lastModified' ] )->diffInMinutes( $timestamp , false ) ) {
                    $unprocessedFiles []= array_merge( $file , [ 'reason' => 'File last modified is more than an hour past it\'s processed time.' ] );

                    continue;
                }
            }

            if ( count( $unprocessedFiles ) ) {
                Notify::log( 'realtime_feed_file_unprocessed' , json_encode( [ "files" => $unprocessedFiles ] ) );
            }
        }
    }
}
