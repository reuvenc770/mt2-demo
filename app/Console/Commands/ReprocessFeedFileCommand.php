<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Services\CMPTE\ReprocessBatchProcessingService;

class ReprocessFeedFileCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:rerunFile {--F|filePath= : Absolute path to the file for processing.} {--D|dirPath= : Absolute path to directory containing files for processing. } {--I|feedId= : Feed ID to process file as.} {--p|party=3 : Party to process file as.} {--H|host= : Remote server\'s host where file lives.} {--P|port= : SSH Port on target server.} {--U|user= : User to use when grabbing data from server. } {--k|publicKey= : Public key to use for SSH connection.} {--K|privateKey= : Private key to use for SSH Connection.} {--R|realtime=0 : Realtime record file flag. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reprocess files on specified server for given feed and party.';

    protected $filePath;
    protected $dirPath;
    protected $host;
    protected $port;
    protected $user;
    protected $publicKey;
    protected $privateKey;
    protected $realtime = 0;

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
    public function handle () {
        $this->processOptions();

        $job = \App::make( \App\Jobs\ReprocessFeedRecordsJob::class , [ str_random( 16 ) ] );

        $job->setServiceNs( $this->getServiceNs() );
        $job->setCreds( $this->host , $this->port , $this->user , $this->publicKey , $this->privateKey );

        if ( !is_null( $this->filePath ) ) {
            $job->setFile( $this->filePath , $this->feedId , $this->party );
        }

        if ( !is_null( $this->dirPath ) ) {
            $job->setFeedDirectory( $this->dirPath , $this->feedId , $this->party );
        }

        $this->dispatch( $job->onQueue( 'rawFeedProcessing' ) );
    }

    protected function getServiceNs () {
        if ( $this->realtime === 1 ) {
            return \App\Services\ReprocessRealtimeProcessingService::class;
        }

        return \App\Services\ReprocessBatchProcessingService::class;
    }

    protected function processOptions () {
        /**
         * File Details
         */
        if ( !is_null( $this->option( 'filePath' ) ) ) {
            $this->filePath = $this->option( 'filePath' );
        }

        if ( !is_null( $this->option( 'dirPath' ) ) ) {
            $this->dirPath = $this->option( 'dirPath' );
        }

        $this->realtime = (int)$this->option( 'realtime' );

        if ( is_null( $this->option( 'feedId' ) ) && $this->realtime ) {
            $this->feedId = 0;
        } elseif ( is_null( $this->option( 'feedId' ) ) ) {
            $this->error( 'Missing Feed ID for processing...' );

            exit();
        } else {
            $this->feedId = (int)$this->option( 'feedId' );
        }

        $this->party = (int)$this->option( 'party' );

        /**
         * Server Details
         */
        if ( is_null( $this->option( 'host' ) ) ) {
            $this->host = 'ssh.servers.mt1_feed_file_server.host';
        } else {
            $this->host = $this->option( 'host' );
        }

        if ( is_null( $this->option( 'port' ) ) ) {
            $this->port = 'ssh.servers.mt1_feed_file_server.port';
        } else {
            $this->port = $this->option( 'port' );
        }

        if ( is_null( $this->option( 'user' ) ) ) {
            $this->user = 'ssh.servers.mt1_feed_file_server.username';
        } else {
            $this->user = $this->option( 'user' );
        }

        if ( is_null( $this->option( 'publicKey' ) ) ) {
            $this->publicKey = 'ssh.servers.mt1_feed_file_server.public_key';
        } else {
            $this->publicKey = $this->option( 'publicKey' );
        }

        if ( is_null( $this->option( 'privateKey' ) ) ) {
            $this->privateKey = 'ssh.servers.mt1_feed_file_server.private_key';
        } else {
            $this->privateKey = $this->option( 'privateKey' );
        }
    }
}
