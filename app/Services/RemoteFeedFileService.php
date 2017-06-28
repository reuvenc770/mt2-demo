<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;
use App\Models\ProcessedFeedFile;
use App\Repositories\RawFeedEmailRepo;
use App\Facades\SlackLevel;

use Carbon\Carbon;
use League\Csv\Reader;
use Cache;
use Illuminate\Support\Facades\Redis;
use Mail;
use Notify;

class RemoteFeedFileService {
    const REDIS_LOCK_KEY_PREFIX = 'feedlock_';

    const DEV_TEAM_EMAIL = 'tech.team.mt2@zetaglobal.com';
    const STAKEHOLDERS_EMAIL = 'orangeac@zetaglobal.com';
    const CLIENT_OPERATOR_EMAIL = 'jherlihy@zetaglobal.com';

    protected $feedService;
    protected $systemService;
    protected $domainGroupService;
    protected $rawRepo;

    protected $newFileList = [];
    protected $lastLineNumber = 0;
    protected $newRecordBuffer = [];

    protected $currentFile = null;
    protected $currentColumnMap = null;
    protected $currentFileLineCount = 0;
    protected $currentFileErrorCount = 0;
    protected $currentLines = null;

    protected $processedFileCount = 0;
    protected $notificationCollection = [];
    protected $missingMappingList = [];

    protected $serviceName = 'RemoteFeedFileService';
    protected $logKeySuffix = '';
    protected $slackChannel = "#mt2team";
    protected $rootFileDirectory = '/home';
    protected $validDirectoryRegex = '/^\/(?:\w+)\/([a-zA-Z0-9_-]+)/';
    protected $lastFileProcessed;
    protected $fileProcessedCallback;
    protected $metaData = [];

    public function __construct ( FeedService $feedService , RemoteLinuxSystemService $systemService , DomainGroupService $domainGroupService , RawFeedEmailRepo $rawRepo ) {
        $this->feedService = $feedService;
        $this->systemService = $systemService;
        $this->domainGroupService = $domainGroupService;
        $this->rawRepo = $rawRepo;
    }

    public function setFileProcessedCallback ( Callable $callback ) {
        $this->fileProcessedCallback = $callback;
    }

    public function processNewFiles () {
        $this->loadNewFilePaths();

        if ( !$this->newFilesPresent() ) {
            \Log::info( $this->serviceName . ': No new files to process....' );
        }

        while ( $this->newFilesPresent() ) {
            $recordSqlList = $this->getNewRecords();
    
            if ( !empty( $recordSqlList ) ) {
                $this->rawRepo->massInsert( $recordSqlList );
            }

            if ( !is_null( $this->lastFileProcessed ) && is_callable( $this->fileProcessedCallback ) ) {
                $callback = $this->fileProcessedCallback;
                $callback( $this->lastFileProcessed , $this->systemService , $this->metaData );

                $this->lastFileProcessed = null;
            }

            $this->processedFileCount++;
        }

        $this->logProcessingComplete();

        $this->logMissingFieldMapping();
    }

    public function updateFeedDirectories () {
        $this->connectToServer();

        $countries = [ 'US' , 'UK' ];
        $isps = $this->domainGroupService->getAllActiveNames();
        $directoryList = $this->getValidDirectories();

        foreach ( $directoryList as $feedDir ) {
            foreach ( $countries as $country ) {
                $country = escapeshellarg( $country );
                $countryDir = "{$feedDir}/{$country}";

                if ( !$this->systemService->directoryExists( $countryDir ) ) {
                    $this->systemService->createDirectory( $countryDir );
                }

                foreach ( $isps as $isp ) {
                    $isp = escapeshellarg( $isp );
                    $ispDir = "{$countryDir}/{$isp}";

                    if( !$this->systemService->directoryExists( $ispDir ) ) {
                        $this->systemService->createDirectory( $ispDir );
                    }
                }
            }
        }
    }

    public function loadNewFilePaths () {
        $this->connectToServer();

        $feedDirList = $this->getValidDirectories();

        foreach ( $feedDirList as $dirInfo ) {
            $newFileString = $this->getRecentFiles( $dirInfo[ 'directory' ] );
            
            $count = 0;
            foreach ( explode( "\n" , $newFileString ) as $newFile ) {
                if (
                    $newFileString !== ''
                    && ProcessedFeedFile::find( trim( $newFile ) ) === null
                    && $count < 10
                ) {
                    $this->newFileList[] = [ 'path' => trim( $newFile ) , 'feedId' => $dirInfo[ 'feedId' ] , 'party' => isset( $dirInfo[ 'party' ] ) ? $dirInfo[ 'party' ] : 3 ];

                    Redis::connection( 'cache' )->executeRaw( [ 'SETNX' , self::REDIS_LOCK_KEY_PREFIX . trim( $newFile ) , getmypid() ] );

                    $count++;
                }
            }
        }
    }

    public function getRecentFiles ( $directory ) {
        return $this->systemService->getRecentFiles( $directory );
    }

    public function newFilesPresent () {
        return count( $this->newFileList ) > 0;
    }

    public function getNewRecords ( $chunkSize = 50000 ) {
        $this->clearRecordBuffer();

        while ( $this->getBufferSize () < $chunkSize ) {
            if ( count( $this->newFileList ) <= 0 ) {
                \Log::info( $this->serviceName . ': No more files to process....' );
                break;
            }

            $this->currentFile = $this->newFileList[ 0 ];

            if ( getmypid() != Redis::connection( 'cache' )->get( self::REDIS_LOCK_KEY_PREFIX . $this->currentFile[ 'path' ] ) ) {
                \Log::debug( 'Reprocess prevented for ' . getmypid() . ' w/ file ' . $this->currentFile[ 'path' ] . '. Lock found....' );

                array_shift( $this->newFileList );

                $this->resetCursor();

                continue;
            }

            \Log::debug( getmypid() . ': ' . $this->currentFile[ 'path' ] );

            $this->currentColumnMap = $this->getFileColumnMap( $this->currentFile[ 'feedId' ] );

            if ( count( $this->currentColumnMap ) <= 0 ) {
                $feedName = $this->feedService->getFeedNameFromId( $this->currentFile[ 'feedId' ] );
                if ( !isset( $this->missingMappingList[ 'files' ] ) ) {
                    $this->missingMappingList[ 'files' ] = [];
                }

                $this->missingMappingList[ 'files' ] []= [
                    "file" => $this->currentFile[ 'path' ] ,
                    "feedId" => $this->currentFile[ 'feedId' ] ,
                    'feedName' => $feedName
                ];

                array_shift( $this->newFileList );
                $this->resetCursor();
                continue;
            }

            if ( $this->lastLineNumber === 0 ) {
                $this->systemService->appendEofToFile( $this->currentFile[ 'path' ] );
            }

            $this->currentFileLineCount = $this->systemService->getFileLineCount( $this->currentFile[ 'path' ] );

            if ( $this->currentFileLineCount === 0 ) {
                $this->markFileAsProcessed();

                Redis::connection( 'cache' )->del( self::REDIS_LOCK_KEY_PREFIX . $this->currentFile[ 'path' ] );

                array_shift( $this->newFileList );

                $this->resetCursor();

                continue;
            }

            $linesLeft = $this->currentFileLineCount - $this->lastLineNumber;
            $linesWanted = $chunkSize - $this->getBufferSize();

            if ( $linesLeft <= $linesWanted ) {
                $this->currentLines = $this->systemService->getFileContentSlice( $this->currentFile[ 'path' ] , ( $this->lastLineNumber + 1 ) , $this->currentFileLineCount );

                $this->processLines();

                $this->markFileAsProcessed();

                Redis::connection( 'cache' )->del( self::REDIS_LOCK_KEY_PREFIX . $this->currentFile[ 'path' ] );
                
                $this->lastFileProcessed = array_shift( $this->newFileList );

                $this->resetCursor();
            }
            else {
                $this->currentLines = $this->systemService->getFileContentSlice( $this->currentFile[ 'path' ] , ( $this->lastLineNumber + 1 ) , ( $linesWanted + $this->lastLineNumber ) );

                $this->processLines();
                
                $this->lastLineNumber = $linesWanted + $this->lastLineNumber;
            }
        }

        return $this->getBufferContent();
    }

    protected function getFileColumnMap ( $feedId ) {
        return $this->feedService->getFileColumnMap( $feedId );
    } 

    protected function processLines () {
        $lineNumber = $this->lastLineNumber + 1;

        foreach( $this->currentLines as $currentLine ) {
            $lineColumns = $this->extractData( $currentLine );

            $this->columnMatchCheck( $lineColumns );

            $record = $this->mapRecord( $lineColumns );

            if ( !is_null( $record ) && $this->isValidRecord( $record , $currentLine , $lineNumber ) ) {
                $this->addToBuffer( $this->rawRepo->toSqlFormat( $record ) );
            }

            $lineNumber++;
        }
    }

    protected function extractData ( $csvLine ) {
        $reader = Reader::createFromString( trim( $csvLine ) );

        if ( strpos( $csvLine , "\t" ) !== false ) {
            $reader->setDelimiter( "\t" );
        }

        return $reader->stripBOM( true )->fetchOne();
    }

    protected function columnMatchCheck ( $lineColumns ) {
        if ( count( $this->currentColumnMap ) !== count( $lineColumns ) ) {
            $errorMessage = $this->serviceName . " Processing Error: Column count does not match for the file '{$this->currentFile[ 'path' ]}'.";

            $this->fireAlert( $errorMessage );

            Notify::log( 'batch_feed_mismatch' , json_encode( [ "files" => [
                'file' => $this->currentFile[ 'path' ] ,
                'feedId' => $this->currentFile[ 'feedId' ] ,
                'feedName' => $this->feedService->getFeedNameFromId( $this->currentFile[ 'feedId' ] )
            ] ] ) );

            throw new \Exception( "\n" . str_repeat( '=' , 150 )  . "\n{$this->serviceName}:\n Column count does not match. Please fix the file '{$this->currentFile[ 'path' ]}' or update the column mapping\n" . str_repeat( '=' , 150 ) );
        } 
    }

    protected function mapRecord ( $lineColumns ) {
        $record = array_combine( $this->currentColumnMap , $lineColumns );
        $record[ 'feed_id' ] = $this->currentFile[ 'feedId' ];
        $record[ 'party' ] = $this->currentFile[ 'party' ];
        $record[ 'realtime' ] = 0;

        if ( !isset( $record[ 'source_url' ] ) || $record[ 'source_url' ] == '' ) {
            $record[ 'source_url' ] = $this->feedService->getSourceUrlFromId( $record[ 'feed_id' ] );
        }

        if ( $record[ 'dob' ] == '0000-00-00' ) {
            unset( $record[ 'dob' ] );
        }

        return $record;
    }

    protected function fireAlert ( $message ) {
        SlackLevel::to( $this->slackChannel )->send( $message );
    }

    protected function isValidRecord ( $record , $rawRecord , $lineNumber ) {
        $validator = \Validator::make( $record , $this->feedService->generateValidationRules( $record ) );

        if ( $validator->fails() ) {
            $this->currentFileErrorCount++;

            $log = $this->rawRepo->logBatchFailure(
                $validator->errors()->toJson() ,
                $rawRecord ,
                $this->currentFile[ 'path' ] ,
                $lineNumber ,
                ( isset( $record[ 'email_address' ] ) ? $record[ 'email_address' ] : '' ) ,
                $record[ 'feed_id' ]
            );

            foreach ( $validator->errors()->messages() as $field => $errorList ) {
                foreach ( $errorList as $currentError ) {
                    if ( preg_match( "/required/" , $currentError ) === 0 ) {
                        $this->rawRepo->logFieldFailure(
                            $field ,
                            $record[ $field ] ,
                            $errorList ,
                            $log->id
                        );

                        break;
                    }
                }
            }

            return false;
        }

        return true;
    }

    protected function markFileAsProcessed () {
        $this->saveFileAsProcessed();
        $this->addFileToNotificationCollection();

        \Log::info( 'RemoteFeedFileService: processed ' . $this->currentFile[ 'path' ] );
    }

    protected function addFileToNotificationCollection () {
        $this->notificationCollection []= [
            "file" =>  substr( strrchr( $this->currentFile[ 'path' ] , "/" ) , 1 ) ,
            "feedId" => $this->currentFile[ 'feedId' ] ,
            "feedName" => $this->feedService->getFeedNameFromId( $this->currentFile[ 'feedId' ] ) ,
            "recordCount" => $this->currentFileLineCount ,
            "errorCount" => $this->currentFileErrorCount ,
            "timeFinished" => Carbon::now()->toDayDateTimeString()
        ];
    }

    protected function saveFileAsProcessed () {
        $cleanPath = trim( $this->currentFile[ 'path' ] );

        \DB::insert( "
            insert into
                processed_feed_files( path , line_count , created_at , updated_at )
            values
                ( '{$cleanPath}' , '{$this->currentFileLineCount}' , NOW() , NOW()  )
            on duplicate key update
                processed_count = processed_count + 1" );
    }

    protected function resetCursor () {
        $this->lastLineNumber = 0;
        $this->currentFileErrorCount = 0;
    }

    protected function addToBuffer ( $record ) {
        $this->newRecordBuffer[] = $record;
    }

    protected function getBufferContent () {
        return $this->newRecordBuffer;
    }

    protected function getBufferSize () {
        return count( $this->newRecordBuffer );
    }

    protected function clearRecordBuffer () {
        $this->newRecordBuffer = [];
    }

    protected function getValidDirectories () {
        $rawDirectoryList = $this->systemService->listDirectories( $this->rootFileDirectory );    

        array_pop( $rawDirectoryList );
        array_shift( $rawDirectoryList );

        $validFeedList = $this->getValidFeedList();

        $directoryList = [];
        
        foreach( $rawDirectoryList as $dir ) { 
            $matches = []; 
            preg_match( $this->validDirectoryRegex , $dir , $matches );

            if ( empty( $matches ) ) {
                continue;
            }

            $notSystemUser = ( strpos( $dir , 'centos' ) === false );
            $notCustomUser = ( strpos( $dir , 'mt2PullUser' ) === false );
            $notAdminUser = ( strpos( $dir , 'sftp-admin' ) === false );
            $isValidFeed = $this->isCorrectDirectoryStructure( $dir ) && in_array( $matches[ 1 ] , $validFeedList );

            if ( $notAdminUser && $notSystemUser && $notCustomUser && $isValidFeed ) { 
                // Need to switch 2430 and 2618 to 2979
                $feedIdResult = $this->getFeedIdFromName( $matches[ 1 ] );
                $feedId = in_array($feedIdResult, [2430, 2618]) ? 2979 : (int)$feedIdResult;

                $directoryList[] = [
                    'directory' => $dir ,
                    'feedId' =>  $feedId ,
                    'party' => $this->feedService->getPartyFromId( $feedId )
                ];
            }   
        }

        \Log::info( json_encode( $directoryList ) );

        return $directoryList;
    }

    protected function isCorrectDirectoryStructure ( $directory ) {
        return ( strpos( $directory , 'upload' ) !== false );
    }

    protected function getValidFeedList () {
        return $this->feedService->getActiveFeedShortNames();
    }

    protected function getFeedIdFromName ( $name ) {
        return $this->feedService->getFeedIdByShortName( $name );
    }

    protected function connectToServer () {
        if ( !$this->systemService->connectionExists() ) {
            $this->systemService->initSshConnection(
                config('ssh.servers.mt1_feed_file_server.host'),
                config('ssh.servers.mt1_feed_file_server.port'),
                config('ssh.servers.mt1_feed_file_server.username'),
                config('ssh.servers.mt1_feed_file_server.public_key'),
                config('ssh.servers.mt1_feed_file_server.private_key')
            );
        }
    }

    protected function logProcessingComplete () {
        if ( $this->processedFileCount > 0 ) {
            Notify::log( 'batch_feed' . $this->logKeySuffix , json_encode( [ "files" => $this->notificationCollection ] ) );
        }
    }

    protected function logMissingFieldMapping () {
        if ( count( $this->missingMappingList ) > 0 ) {
            Notify::log( 'batch_feed_mapping_missing' , json_encode( $this->missingMappingList ) );
        }
    }
}
