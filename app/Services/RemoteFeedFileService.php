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

class RemoteFeedFileService {
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
        foreach( $this->currentLines as $currentLine ) {
            $lineColumns = explode( ',' , $currentLine );

            if ( count( $this->currentColumnMap ) !== count( $lineColumns ) ) {
                throw new \Exception( "\n" . str_repeat( '=' , 150 )  . "\nRemoteFeedFileService:\n Column count does not match. Please fix the file '{$this->currentFile[ 'path' ]}' or update the column mapping\n" . str_repeat( '=' , 150 ) );
            } 

            $record = array_combine( $this->currentColumnMap , $lineColumns );
            $record[ 'feed_id' ] = $this->currentFile[ 'feedId' ];

            $this->addToBuffer( $this->rawRepo->toSqlFormat( $record ) );
        }
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

        $validFeedList = $this->feedService->getActiveFeedNames();

        $directoryList = [];
        
        foreach( $rawDirectoryList as $dir ) { 
            $matches = []; 
            preg_match( '/^(?:.+\/)(?:\.{0,})([\w\s]+)$/' , $dir , $matches );

            $notSystemUser = ( strpos( $dir , 'centos' ) === false );
            $notCustomUser = ( strpos( $dir , 'mt2PullUser' ) === false );
            $isValidFeed = in_array( $matches[ 1 ] , $validFeedList );
            if ( $notSystemUser && $notCustomUser && $isValidFeed ) { 
                $directoryList[] = [ 'directory' => $dir , 'feedId' => $this->feedService->getFeedIdByName( $matches[ 1 ] ) ];
            }   
        }

        return $directoryList;
    }

    protected function connectToServer () {
        if ( !$this->systemService->connectionExists() ) {
            $this->systemService->initSshConnection(
                env( 'FEED_FILE_HOST' ) ,
                env( 'FEED_FILE_PORT' ) ,
                env( 'FEED_FILE_USER' ) ,
                env( 'FEED_FILE_PUB_KEY' ) ,
                env( 'FEED_FILE_PRIV_KEY' )
            );
        }
    }
}
