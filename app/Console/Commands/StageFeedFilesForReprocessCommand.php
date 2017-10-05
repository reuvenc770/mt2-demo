<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class StageFeedFilesForReprocessCommand extends Command
{
    use DispatchesJobs;

    protected $signature = 'feedRecords:grabFiles {--R|realtime : File source is the realtime server. } {--B|batch : File source is the batch server. } {--N|fileName= : Filename to search for. } {--D|dirPath= : Directory path for copying files from. } {--f|feedId= : Feed ID for batch file. Defaulted to 0 for realtime files. } {--m|minAge= : Minimum age in days of files to copy. } {--M|maxAge= : Max age in days of files to copy. }';

    protected $description = <<<DSC
Utility command for copying batch or realtime files to the standard CMP SFTP server for reprocessing.

Realtime Destination: sftp-01.mtroute.com:/home/sftp-admin/reprocess/realtime/queued
Batch Destination: sftp-01.mtroute.com:/home/sftp-admin/reprocess/batch/queued/#feedname#

Realtime Examples:

    Single Realtime File By Name:
    php artisan feedRecords:grabFiles --realtime --fileName=realtime_aspiremail.mtroute.net_20177251430.dat  

    Realtime w/ Min Age - All realtime files in the older than 2 days:
    php artisan feedRecords:grabFiles --realtime --minAge=2 

    Realtime w/ Max Age - All realtime files in the past 2 days:
    php artisan feedRecords:grabFiles --realtime --maxAge=2 

    Realtime w/ Age Range - All realtime files that are at least 2 days old but no older than 4 days:
    php artisan feedRecords:grabFiles --realtime --minAge=2 --maxAge=4 


Batch Examples:

    Single Batch File By Name:
    php artisan feedRecords:grabFiles --batch --feedId=9999 --fileName=SPI160306_dc.txt  

    Batch w/ Min Age - All realtime files in the older than 2 days:
    php artisan feedRecords:grabFiles --batch --feedId=9999 --minAge=2 

    Batch w/ Max Age - All realtime files in the past 2 days:
    php artisan feedRecords:grabFiles --batch --feedId=9999 --maxAge=2 

    Batch w/ Age Range - All realtime files that are at least 2 days old but no older than 4 days:
    php artisan feedRecords:grabFiles --batch --feedId=9999 --minAge=2 --maxAge=4 
DSC;

    protected $sourceType;
    protected $feedId;

    protected $fileName;
    protected $filePath;

    protected $directoryPath;

    protected $minAge;
    protected $maxAge;

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->processOptions();

        $this->dispatch( $this->getJob() );
    }

    public function getJob () {
        $job = \App::make( \App\Jobs\StageFeedFilesForReprocessJob::class , [ str_random( 16 ) ] );

        if ( $this->sourceType === 'realtime' ) {
            if ( !is_null( $this->fileName ) ) {
                $job->setupRealtimeFile( $this->fileName );

                return $job;
            }

            $job->setupRealtimeDirectory( $this->directoryPath , $this->minAge , $this->maxAge );

            return $job;
        } else {
            if ( !is_null( $this->fileName ) ) {
                $job->setupBatchFile( $this->fileName , $this->feedId );

                return $job;
            }

            $job->setupBatchDirectory( $this->feedId , $this->minAge , $this->maxAge );

            return $job;
        }
    }

    public function processOptions () {
        if ( !is_null( $this->option( 'realtime' ) ) ) {
            $this->sourceType = 'realtime';
        }

        if ( !is_null( $this->option( 'batch' ) ) ) {
            $this->sourceType = 'batch';
        }

        if ( is_null( $this->sourceType ) ) {
            $this->error( 'StageFeedFilesForReprocessCommand: Source Type is Required.' );
            exit();
        }

        if ( !is_null( $this->option( 'feedId' ) ) ) {
            $this->feedId = $this->option( 'feedId' );
        }

        if ( $this->sourceType = 'batch' && is_null( $this->feedId ) ) {
            $this->error( 'StageFeedFilesForReprocessCommand: Batch mode requires a feed ID.' );
            exit();
        }

        if ( !is_null( $this->option( 'fileName' ) ) ) { 
            $this->fileName = $this->option( 'fileName' );
        }

        if ( !is_null( $this->option( 'dirPath' ) ) ) {
            $this->directoryPath = $this->option( 'dirPath' );
        }

        if ( !is_null( $this->option( 'minAge' ) ) ) {
            $this->minAge = (int)$this->option( 'minAge' );
        }

        if ( !is_null( $this->option( 'maxAge' ) ) ) {
            $this->maxAge = (int)$this->option( 'maxAge' );
        }
    }
}
