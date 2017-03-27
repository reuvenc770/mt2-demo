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

class RemoteFeedFileService {
    const SLACK_CHANNEL = "#mt2team";

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

    public function __construct ( FeedService $feedService , RemoteLinuxSystemService $systemService , DomainGroupService $domainGroupService , RawFeedEmailRepo $rawRepo ) {
        $this->feedService = $feedService;
        $this->systemService = $systemService;
        $this->domainGroupService = $domainGroupService;
        $this->rawRepo = $rawRepo;
    }

    public function processNewFiles () {
        $this->loadNewFilePaths();

        while ( $this->newFilesPresent() ) {
            $recordSqlList = $this->getNewRecords();

            $this->rawRepo->massInsert( $recordSqlList );
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
                \Log::info( 'RemoteFeedFileService: No files to process....' );
                break;
            }

            $this->currentFile = $this->newFileList[ 0 ];
            $this->currentColumnMap = $this->feedService->getFileColumnMap( $this->currentFile[ 'feedId' ] );

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
                
                array_shift( $this->newFileList );

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

    protected function processLines () {
        $lineNumber = $this->lastLineNumber + 1;

        foreach( $this->currentLines as $currentLine ) {
            $lineColumns = explode( ',' , $currentLine );

            if ( count( $this->currentColumnMap ) !== count( $lineColumns ) ) {
                SlackLevel::to(self::SLACK_CHANNEL)->send( "Feed File Processing Error: Column count does not match for the file '{$this->currentFile[ 'path' ]}'." );

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
        $rawDirectoryList = $this->systemService->listDirectories( '/home' );    

        array_pop( $rawDirectoryList );
        array_shift( $rawDirectoryList );

        $validFeedList = $this->feedService->getActiveFeedShortNames();

        $directoryList = [];
        
        foreach( $rawDirectoryList as $dir ) { 
            $matches = []; 
            preg_match( '/^\/(?:\w+)\/([a-zA-Z0-9_-]+)/' , $dir , $matches );

            $notSystemUser = ( strpos( $dir , 'centos' ) === false );
            $notCustomUser = ( strpos( $dir , 'mt2PullUser' ) === false );
            $notAdminUser = ( strpos( $dir , 'sftp-admin' ) === false );
            $isValidFeed = ( strpos( $dir , 'upload' ) !== false ) && in_array( $matches[ 1 ] , $validFeedList );

            if ( $notAdminUser && $notSystemUser && $notCustomUser && $isValidFeed ) { 
                // Need to switch 2430 and 2618 to 2979
                $feedIdResult = $this->feedService->getFeedIdByShortName( $matches[ 1 ] );
                $feedId = in_array($feedIdResult, [2430, 2618]) ? 2979 : (int)$feedIdResult;
                $directoryList[] = [ 'directory' => $dir , 'feedId' =>  $feedId];
            }   
        }

        return $directoryList;
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
