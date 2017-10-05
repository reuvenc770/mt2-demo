<?php

namespace App\Jobs;

use App\Jobs\MonitoredJob;

class StageFeedFilesForReprocessJob extends MonitoredJob
{
    const CONF_SFTP_SERVER = 'ssh.servers.mt1_feed_file_server.';
    const CONF_REALTIME = 'ssh.servers.cmpte_realtime_server.';
    const CONF_BATCH = 'ssh.servers..'; #need to add record proc to config and test to see if prod can communicate w/ it.

    const CONF_KEY_HOST = 'host';
    const CONF_KEY_PORT = 'port';
    const CONF_KEY_USER = 'username';
    const CONF_KEY_PUBLICKEY = 'public_key';
    const CONF_KEY_PRIVATEKEY = 'private_key';

    const SOURCE_BASE_PATH_REALTIME = '/var/local/programdata/done/mt2_realtime/';
    const SOURCE_BASE_PATH_BATCH = '/home/';

    const DEST_BASE_PATH_REALTIME = '/home/sftp-admin/reprocess/realtime/queued/';
    const DEST_BASE_PATH_BATCH = '/home/sftp-admin/reprocess/batch/queued/';

    const TMP_STORAGE_REALTIME = '/tmp/reprocess/realtime';
    const TMP_STORAGE_BATCH = '/tmp/reprocess/batch';

    const TYPE_REALTIME = 'realtime';
    const TYPE_BATCH = 'batch';

    const MODE_FILE = 'file';
    const MODE_DIRECTORY = 'directory';

    protected $jobName = 'StageFeedFilesForReprocessJob-';
    protected $tracking;

    protected $remoteService;
    protected $feedService;

    protected $mode;
    protected $modeList = [ 'file' , 'directory' ];

    protected $type;
    protected $typeList = [ 'batch' , 'realtime' ];

    protected $feedId;

    protected $sourceConn;
    protected $sourceFile;
    protected $sourcePath;

    protected $destinationConn;
    protected $destinationFile;
    protected $destincationPath;

    protected $minAge;
    protected $maxAge;

    protected $setupComplete = false;

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

    public function handleJob()
    {
        if ( !$this->setupComplete() ) {
            throw new \Exception( "StageFeedFilesForReprocessJob: Initial state not setup." );
        }

        $this->remoteService = \App::make( \App\Services\RemoteLinuxSystemService::class );
        $this->feedService = \App::make( \App\Services\FeedService::class );

        $this->setupSshConnections();

        $this->{$this->handlerName}();
    }

    public function setupComplete () {
        return $this->setupComplete === true;
    }

    public function setupRealtimeFile ( $fileName ) {
        $this->type = self::TYPE_REALTIME; 
        $this->mode = self::MODE_FILE;

        $this->sourceFile = self::SOURCE_BASE_PATH_REALTIME . $fileName; 
        $this->temporaryFile = self::TMP_STORAGE_REALTIME . $filename;
        $this->destinationFile = self::DEST_BASE_PATH_REALTIME . $fileName;

        $this->handlerName = 'runTransfer';

        $this->setupComplete = true;
    }

    public function setupRealtimeDirectory ( $directory = null , $minAge = null , $maxAge = null ) {
        $this->type = self::TYPE_REALTIME; 
        $this->mode = self::MODE_DIRECTORY;

        $this->sourcePath = ( is_null( $directory ) ? self::SOURCE_BASE_PATH_REALTIME : $directory );
        $this->minAge = ( is_null( $minAge ) ? null : $minAge );
        $this->maxAge = ( is_null( $maxAge ) ? null : $maxAge );

        $this->handlerName = 'transferRealtimeDirectoryContents';

        $this->setupComplete = true;
    }

    public function setupBatchFile ( $fileName , $feedId ) {
        $this->type = self::TYPE_BATCH; 
        $this->mode = self::MODE_FILE;

        $this->feedId = $feedId;
        $feedName = $this->feedService->getFeedNameFromId( $feedId );

        $this->sourceFile = self::SOURCE_BASE_PATH_BATCH . "{$feedName}/{$fileName}"; 
        $this->temporaryFile = self::TMP_STORAGE_BATCH . $filename;
        $this->destinationFile = self::DEST_BASE_PATH_BATCH . "{$feedName}/{$fileName}";

        $this->handlerName = 'runTransfer';

        $this->setupComplete = true;
    }

    public function setupBatchDirectory ( $feedId , $minAge = null , $maxAge = null ) {
        $this->type = self::TYPE_BATCH; 
        $this->mode = self::MODE_DIRECTORY;

        $this->feedId = $feedId;
        $feedName = $this->feedService->getFeedNameFromId( $feedId );

        $this->sourcePath =  self::SOURCE_BASE_PATH_BATCH . "{$feedName}/";
        $this->minAge = ( is_null( $minAge ) ? null : $minAge );
        $this->maxAge = ( is_null( $maxAge ) ? null : $maxAge );

        $this->handlerName = 'transferBatchDirectoryContents';

        $this->setupComplete = true;
    }

    protected function runTransfer () {
        $this->transferFile( $this->sourceFile , $this->temporaryFile , $this->destinationFile )
    }

    protected function transferRealtimeDirectoryContents () {
        foreach ( $this->getFiles() as $current ) {
            $this->transferFile(
                $current->path ,
                self::TMP_STORAGE_REALTIME . $current->name ,
                self::DEST_BASE_PATH_REALTIME . $current->name
            );
        }
    }

    protected function transferBatchDirectoryContents () {
        $feedName = $this->feedService->getFeedNameFromId( $this->feedId );
        $tmpPath = self::TMP_STORAGE_BATCH . "{$feedName}/";

        if ( !file_exists( $tmpPath ) ) {
            mkdir( $tmpPath );
        }

        $this->remoteService->setConnection( $this->destinationConn );
        $destPath = self::DEST_BASE_PATH_BATCH . "{$feedName}/";

        if ( !$this->remoteService->directoryExists( $destPath ) ) {
             $this->remoteService->createDirectory( $destPath );
        }

        foreach ( $this->getFiles() as $current ) {
            $this->transferFile(
                $current->path ,
                $tmpPath . $current->name ,
                $destPath . $current->name
            );
        }
    }

    protected function setupSshConnections () {
        $sourceConf = ( $this->isRealtime() ? self::CONF_REALTIME : self::CONF_BATCH );

        $this->sourceConn = $this->makeSshConnection(
            config( $sourceConf . self::CONF_KEY_HOST ) ,
            config( $sourceConf . self::CONF_KEY_PORT ) ,
            config( $sourceConf . self::CONF_KEY_USER ) ,
            config( $sourceConf . self::CONF_KEY_PUBLICKEY ) ,
            config( $sourceConf . self::CONF_KEY_PRIVATEKEY )
        );

        $this->destinationConn =  $this->makeSshConnection(
            config( self::CONF_SFTP_SERVER . self::CONF_KEY_HOST ) ,
            config( self::CONF_SFTP_SERVER . self::CONF_KEY_PORT ) ,
            config( self::CONF_SFTP_SERVER . self::CONF_KEY_USER ) ,
            config( self::CONF_SFTP_SERVER . self::CONF_KEY_PUBLICKEY ) ,
            config( self::CONF_SFTP_SERVER . self::CONF_KEY_PRIVATEKEY )
        );
    }

    protected function isRealtime () {
        return ( $this->type === self::TYPE_REALTIME );
    }

    protected function makeSshConnection ( $host , $port , $username , $publicKey , $privateKey ) {
        $connection = ssh2_connect( $host , $port , [ 'hostkey' => 'ssh-rsa' ] );

        if ( $connection === false ) {
            throw new \Exception( "StageFeedFilesForReprocessJob: Failed to connect to server: {$user}@{$host}:{$port}" );
        }

        $authSuccess = ssh2_auth_pubkey_file(
            $connection ,
            $username ,
            $publicKey ,
            $privateKey
        );

        if ( $authSuccess === false ) {
            throw new \Exception( "StageFeedFilesForReprocessJob: Failed to authenticate with the server." );
        }

        return $connection;
    }

    protected function transferFile ( $sourceFile , $tempFile , $destFile ) {
        ssh2_scp_recv( $this->sourceConn , $sourceFile , $tempFile );

        ssh2_scp_send( $this->destConn , $tempFile , $destFile );
    }

    protected function getFiles () {
        $newFileString = $this->remoteService->getRecentFiles( $this->sourcePath , $this->getFindOptions() );
        $matches = [];
        
        foreach ( explode( "\n" , $newFileString ) as $newFile ) {
            if (
                $newFile !== '' #empty line check
                && strpos( $newFile , "find:" ) !== 0 #error
                && preg_match( '/^.*\/$/' , $newFile , $matches ) === 0 #directory check
            ) {
                $matches = [];

                preg_match( '/.*\/(.*\.[a-z]{3})$/' , $newFile , $matches );

                $object = new StdClass;
                $object->path = $newFile;
                $object->name = $matches[ 1 ];

                return $object;
            }
        }
    }

    protected function getFindOptions () {
        $findOptions = [ '-type f' , '-print' ];

        if ( !is_null( $this->minAge ) ) {
            $findOptions []= '+' . $this->minAge;
        }

        if ( !is_null( $this->maxAge ) ) {
            $findOptions []= '-' . $this->maxAge;
        }

        return $findOptions;
    }
}
