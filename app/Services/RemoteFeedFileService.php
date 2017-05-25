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
use Mail;

class RemoteFeedFileService {
    const DEV_TEAM_EMAIL = 'tech.team.mt2@zetaglobal.com';
    const STAKEHOLDERS_EMAIL = 'orangeac@zetaglobal.com';

    protected $feedService;
    protected $systemService;
    protected $domainGroupService;
    protected $rawRepo;

    protected $newFileList = [];
    protected $lastLineNumber = 0;
    protected $newRecordBuffer = [];

    protected $currentFile = null;
    protected $currentColumnMap = null;
    protected $currentFileLineCount = null;
    protected $currentLines = null;

    protected $processedFileCount = 0;
    protected $notificationCollection = [];

    protected $slackChannel = "#mt2team";
    protected $rootFileDirectory = '/home';
    protected $validDirectoryRegex = '/^\/(?:\w+)\/([a-zA-Z0-9_-]+)/';
    protected $lastFileProcessed;
    protected $fileProcessedCallback;

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
            \Log::info( 'RemoteFeedFileService: No new files to process....' );
        }

        while ( $this->newFilesPresent() ) {
            $recordSqlList = $this->getNewRecords();

            $this->rawRepo->massInsert( $recordSqlList );

            if ( !is_null( $this->lastFileProcessed ) && is_callable( $this->fileProcessedCallback ) ) {
                $callback = $this->fileProcessedCallback;
                $callback( $this->lastFileProcessed , $this->systemService);

                $this->lastFileProcessed = null;
            }

            $this->processedFileCount++;
        }

        if ( $this->processedFileCount > 0 ) {
            Mail::raw( implode( PHP_EOL , $this->notificationCollection )  , function ( $message ) {
                $message->to( self::DEV_TEAM_EMAIL );
                #$message->to( self::STAKEHOLDERS_EMAIL );
                
                $message->subject( 'Feed Files Processed - ' . Carbon::now()->toCookieString() );
                $message->priority(1);
            } );
        }
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
            $newFileString = $this->systemService->getRecentFiles( $dirInfo[ 'directory' ] );
            
            foreach ( explode( "\n" , $newFileString ) as $newFile ) {
                if ( $newFile !== '' && ProcessedFeedFile::find( $newFile ) === null ) {
                    $this->newFileList[] = [ 'path' => $newFile , 'feedId' => $dirInfo[ 'feedId' ] ];
                }
            }
        }
    }

    public function newFilesPresent () {
        return count( $this->newFileList ) > 0;
    }

    public function getNewRecords ( $chunkSize = 50000 ) {
        $this->clearRecordBuffer();

        while ( $this->getBufferSize () < $chunkSize ) {
            if ( count( $this->newFileList ) <= 0 ) {
                \Log::info( 'RemoteFeedFileService: No more files to process....' );
                break;
            }

            $this->currentFile = $this->newFileList[ 0 ];
            $this->currentColumnMap = $this->getFileColumnMap( $this->currentFile[ 'feedId' ] );

            if ( $this->lastLineNumber === 0 ) {
                $this->systemService->appendEofToFile( $this->currentFile[ 'path' ] );
            }

            $this->currentFileLineCount = $this->systemService->getFileLineCount( $this->currentFile[ 'path' ] );

            if ( $this->currentFileLineCount === 0 ) {
                $this->markFileAsProcessed();

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
            $lineColumns = explode( ',' , trim( $currentLine ) );

            if ( count( $this->currentColumnMap ) !== count( $lineColumns ) ) {
                $this->fireAlert( "Feed File Processing Error: Column count does not match for the file '{$this->currentFile[ 'path' ]}'." );

                throw new \Exception( "\n" . str_repeat( '=' , 150 )  . "\nRemoteFeedFileService:\n Column count does not match. Please fix the file '{$this->currentFile[ 'path' ]}' or update the column mapping\n" . str_repeat( '=' , 150 ) );
            } 

            $record = array_combine( $this->currentColumnMap , $lineColumns );
            $record[ 'feed_id' ] = $this->currentFile[ 'feedId' ];

            if ( $this->isValidRecord( $record , $currentLine , $lineNumber ) ) {
                $this->addToBuffer( $this->rawRepo->toSqlFormat( $record ) );
            }

            $lineNumber++;
        }
    }

    protected function fireAlert ( $message ) {
        SlackLevel::to( $this->slackChannel )->send( $message );
    }

    protected function isValidRecord ( $record , $rawRecord , $lineNumber ) {
        $validator = \Validator::make( $record , $this->feedService->generateValidationRules( $record ) );

        if ( $validator->fails() ) {
            $log = $this->rawRepo->logBatchFailure(
                $validator->errors()->toJson() ,
                $rawRecord ,
                $this->currentFile[ 'path' ] ,
                $lineNumber ,
                ( $record[ 'email_address' ] ? : '' ) ,
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
        ProcessedFeedFile::updateOrCreate( [ 'path' => $this->currentFile[ 'path' ] ] , [
            'path' => $this->currentFile[ 'path' ] ,
            'feed_id' => $this->currentFile[ 'feedId' ] ,
            'line_count' => $this->currentFileLineCount
        ] );

        $this->notificationCollection []= "File {$this->currentFile[ 'path' ]} from '"
            . $this->feedService->getFeedNameFromId( $this->currentFile[ 'feedId' ] )
            . "' ({$this->currentFile[ 'feedId' ]}) was processed at "
            . Carbon::now()->toCookieString() . " with {$this->currentFileLineCount} records.";


        \Log::info( 'RemoteFeedFileService: processed ' . $this->currentFile[ 'path' ] );
    }

    protected function resetCursor () {
        $this->lastLineNumber = 0;
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

            $notSystemUser = ( strpos( $dir , 'centos' ) === false );
            $notCustomUser = ( strpos( $dir , 'mt2PullUser' ) === false );
            $notAdminUser = ( strpos( $dir , 'sftp-admin' ) === false );
            $isValidFeed = $this->isCorrectDirectoryStructure( $dir ) && in_array( $matches[ 1 ] , $validFeedList );

            if ( $notAdminUser && $notSystemUser && $notCustomUser && $isValidFeed ) { 
                // Need to switch 2430 and 2618 to 2979
                $feedIdResult = $this->getFeedIdFromName( $matches[ 1 ] );
                $feedId = in_array($feedIdResult, [2430, 2618]) ? 2979 : (int)$feedIdResult;
                $directoryList[] = [ 'directory' => $dir , 'feedId' =>  $feedId];
            }   
        }

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
}
