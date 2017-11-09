<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;

class ReprocessFeedRecordsJob extends MonitoredJob
{
    protected $jobName = 'ReprocessFeedRecordsJob-';
    protected $tracking;

    protected $serviceNs;
    protected $service;

    protected $host;
    protected $port;
    protected $user;
    protected $publicKey;
    protected $privateKey;

    protected $filePath;
    protected $feedDirectoryPath;
    protected $feedId;
    protected $party; 

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $tracking , $runtimeThreshold="15m" )
    {
        $this->tracking = $tracking;
        $this->jobName .= $tracking;

        parent::__construct(
            $this->jobName , 
            $runtimeThreshold ,
            $tracking
        );
    }

    public function setServiceNs ( $serviceNs ) {
        $this->serviceNs = $serviceNs;
    }

    public function setCreds ( $host , $port , $user , $publicKey , $privateKey ) {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->publicKey = $publicKey;
        $this->privateKey = $privateKey;
    }

    public function setFile ( $filePath , $feedId , $party ) {
        $this->filePath = $filePath;
        $this->feedId = $feedId;
        $this->party = $party; 
    }

    public function setFeedDirectory ( $feedDirectory , $feedId , $party ) {
        $this->feedDirectoryPath = $feedDirectory;
        $this->feedId = $feedId;
        $this->party = $party; 
    }

    public function handleJob()
    {
        $this->initService();

        $this->loadFilesIntoService();

        $this->service->processNewFiles();
    }

    protected function initService () {
        $this->checkServiceConfig();
        $this->service = \App::make( $this->serviceNs );

        $this->checkServerConfig();
        $this->service->setCreds( $this->host , $this->port , $this->user , $this->publicKey , $this->privateKey );

        $this->service->setFileProcessedCallback( function ( $files , $systemService , $meta ) {
            $message = "ReprocessFeedRecordsJob\nReprocessed the following files:\n```";

            foreach ( $files as $file ) {
                $message .= "\t{$file[ 'path' ]}\n";
            }

            $message .= "```";

            \Maknz\Slack\Facades\Slack::to( config( 'slack.errorChannel' ) )->send( $message );
        } );
    }

    protected function checkServiceConfig () {
        if ( is_null( $this->serviceNs ) ) {
            throw new \Exception( 'ReprocessFeedRecordsJob - Service Namespace required.' );
        }
    }

    protected function checkServerConfig () {
        if (
            is_null( $this->host )
            || is_null( $this->port )
            || is_null( $this->user )
            || is_null( $this->publicKey)
            || is_null( $this->privateKey )
        ) {
            throw new \Exception( 'ReprocessFeedRecordsJob - Missing server creds.' );
        }
    }

    protected function loadFilesIntoService () {
        $this->checkFileConfig();

        $this->loadSingleFileIntoService();

        $this->loadFilesFromDirectoryIntoService();
    }

    protected function checkFileConfig () {
        if ( is_null( $this->feedId ) && !$this->isRealtime() ) {
            throw new \Exception( 'ReprocessFeedRecordsJob - Feed ID required.' );
        }

        if ( is_null( $this->party ) ) {
            throw new \Exception( 'ReprocessFeedRecordsJob - Feed Party required.' );
        }

        if ( is_null( $this->filePath ) && is_null( $this->feedDirectoryPath ) ) {
            throw new \Exception( 'ReprocessFeedRecordsJob - Must specify either a file or a directory for processing.' );
        }
    }

    protected function isRealtime () {
        return ( $this->serviceNs === \App\Services\ReprocessRealtimeProcessingService::class );
    }

    protected function loadSingleFileIntoService () {
        if ( !is_null( $this->filePath ) ) {
            $this->service->setFile( $this->filePath , $this->feedId , $this->party );
        }
    }

    protected function loadFilesFromDirectoryIntoService () {
        if ( !is_null( $this->feedDirectoryPath ) ) {
            foreach( $this->service->getAllFilesFromDir( $this->feedDirectoryPath ) as $currentDir ) {
                $this->service->setFile( $currentDir , $this->feedId , $this->party );
            }
        }
    }
}
