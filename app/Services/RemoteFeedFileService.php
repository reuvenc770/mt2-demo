<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;

class RemoteFeedFileService {
    protected $feedService;
    protected $systemService;
    protected $domainGroupService;

    protected $newFileList = [];
    protected $newRecordBuffer = [];

    public function __construct ( FeedService $feedService , RemoteLinuxSystemService $systemService , DomainGroupService $domainGroupService ) {
        $this->feedService = $feedService;
        $this->systemService = $systemService;
        $this->domainGroupService = $domainGroupService;
    }

    public function init ( $host = null , $port = null , $user = null , $pubKey = null , $privKey = null ) {
        if ( !is_null( $host ) && !is_null( $port ) && !is_null( $user ) && !is_null( $pubKey ) && !is_null( $privKey ) ) {
            $this->systemService->init( $host , $port , $user , $pubKey , $privKey );

            return true;
        }

        $this->systemService->init( env( 'FEED_FILE_HOST' ) , env( 'FEED_FILE_PORT' ) , env( 'FEED_FILE_USER' ) , env( 'FEED_FILE_PUB_KEY' ) , env( 'FEED_FILE_PRIV_KEY' ) );

        return true;
    }

    public function updateFeedDirectories () {
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

    public function getNewRecords ( $chunkSize = 50000 ) {
        $this->clearRecordBuffer();

        if ( empty( $this->newFileList ) ) {
            $this->getNewFilePaths();
        }

        \Log::info( $this->newFileList );

        /*
        while ( $this->getBufferSize () < $chunkSize ) {

        }

        return $this->getBufferContent();
         */
    }

    public function addToBuffer ( $record ) {
        $this->newRecordBuffer[] = $record;
    }

    public function getBufferContent () {
        return $this->newRecordBuffer;
    }

    public function getBufferSize () {
        return count( $this->newRecordBuffer );
    }

    public function clearRecordBuffer () {
        $this->newRecordBuffer = [];
    }

    protected function getNewFilePaths () {
        $feedDirList = $this->getValidDirectories();

        foreach ( $feedDirList as $dirInfo ) {
            $countryDirList = $this->systemService->listDirectories( $dirInfo[ 'directory' ] );
            $newFileString = $this->systemService->getRecentFiles( $dirInfo[ 'directory' ] );
            
            foreach ( explode( "\n" , $newFileString ) as $newFile ) {
                if ( $newFile !== '' ) { $this->newFileList[] = [ 'directory' => $newFile , 'feedId' => $dirInfo[ 'feedId' ] ]; }
            }
        }
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
}
