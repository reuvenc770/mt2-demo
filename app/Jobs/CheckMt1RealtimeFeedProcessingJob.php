<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\MonitoredJob;
use App\Models\ProcessedFeedFile;
use Maknz\Slack\Facades\Slack;
use Carbon\Carbon;
use Notify;

class CheckMt1RealtimeFeedProcessingJob extends MonitoredJob {
    protected $jobName = 'CheckMt1RealtimeFeedProcessingJob';
    protected $tracking;

    public function __construct ( $tracking , $runtimeThreshold="15m") {
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
        $this->feedFileService = \App::make( \App\Services\ProcessedFeedFileService::class ); 

        $this->remote->initSshConnection(
            config('ssh.servers.cmpte_realtime_server.host'),
            config('ssh.servers.cmpte_realtime_server.port'),
            config('ssh.servers.cmpte_realtime_server.username'),
            config('ssh.servers.cmpte_realtime_server.public_key'),
            config('ssh.servers.cmpte_realtime_server.private_key')
        );

        $this->findUnprocessedRealtimeFiles();
        
        $this->checkForNoDataStream();
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
    
    protected function checkForNoDataStream () {
        if ( ProcessedFeedFile::whereRaw( 'created_at >= NOW() - interval 2 HOUR' )->count() == 0 ) {
            Slack::to( self::SLACK_CHANNEL )->send( "No Record Data coming in...please investigate!" );
        }
    }
}
