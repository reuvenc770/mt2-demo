<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Services\RemoteFeedFileService;
use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;
use App\Repositories\RawFeedEmailRepo;

use League\Csv\Reader;
use Notify;
use Carbon\Carbon;

class Mt1RealtimeProcessingService extends RemoteFeedFileService {
    protected $serviceName = 'Mt1RealtimeProcessingService';
    protected $logKeySuffix = '_realtime';
    protected $slackChannel = '#cmp_hard_start_errors';
    protected $currentCustomFields = [];
    protected $recordCounts = [];

    protected $realtimeDirectory = '/var/local/programdata/done/mt2_realtime';
    protected $realtimeFeedId = 0; #feed IDs are per record
    protected $realtimeFileColumnMap = [
        'feed_id' ,
        'email_address' ,
        'gender' ,
        'first_name' ,
        'last_name' ,
        'dob' ,
        'address' ,
        'address2' ,
        'city' ,
        'state' ,
        'zip' ,
        'country' ,
        'phone' ,
        'ip' ,
        'source_url' ,
        'domain_id' ,
        'cha_news' ,
        'capture_date' ,
    ];

    public function __construct (
        FeedService $feedService , 
        RemoteLinuxSystemService $systemService ,
        DomainGroupService $domainGroupService ,
        RawFeedEmailRepo $rawRepo
    ) {
        parent::__construct(
            $feedService ,
            $systemService ,
            $domainGroupService ,
            $rawRepo
        );
    }

    public function fireAlert ( $message ) {
        Slack::to( $this->slackChannel )->send( $message );
    }

    protected function connectToServer () {
        if ( !$this->systemService->connectionExists() ) { 
            $this->systemService->initSshConnection(
                config('ssh.servers.cmpte_realtime_server.host'),
                config('ssh.servers.cmpte_realtime_server.port'),
                config('ssh.servers.cmpte_realtime_server.username'),
                config('ssh.servers.cmpte_realtime_server.public_key'),
                config('ssh.servers.cmpte_realtime_server.private_key')
            );  
        }   
    }

    protected function getValidDirectories () {
        return [
            [ 'directory' => $this->realtimeDirectory , 'feedId' => $this->realtimeFeedId ]
        ];
    }

    protected function getFileColumnMap ( $feedId ) {
        return $this->realtimeFileColumnMap;
    }

    protected function extractData ( $csvLine ) {
        $reader = Reader::createFromString( trim( $csvLine ) );
        $reader->setDelimiter( '|' );
        $columns = $reader->fetchOne();

        #ipv6 fix, add colons
        if ( isset( $columns[ 13 ] ) && strlen( $columns[ 13 ] ) == 32 ) {
            $columns[ 13 ] = substr( chunk_split( $columns[ 13 ] , 4 , ':' ) , 0 , -1 ); 
        }
        
        $extraFields = [];

        #turn into a query string and parse into associative array
        parse_str( str_replace( ';' , '&' , $columns[ count( $columns ) - 1 ] ) , $extraFields );

        #store field names for later when mapping
        $this->currentCustomFields = array_keys( $extraFields );

        #remove last item that contains custom fields
        array_pop( $columns );

        array_push( $columns , \Carbon\Carbon::now()->toDateTimeString() );

        #merge both standard and custom fields
        return array_merge( $columns , array_values( $extraFields ) );
    }

    protected function mapRecord ( $lineColumns ) {
        $currentColumnMap = array_merge( $this->currentColumnMap , $this->currentCustomFields );

        if ( count( $currentColumnMap ) != count( $lineColumns ) ) {
            $this->logFailure(
                'Error when parsing line. Zipping columns with values failed.' ,
                json_encode( $lineColumns ) ,
                $this->currentFile[ 'path' ] , 
                0 ,
                '' , 
                $this->getFeedIdFromRecord( $lineColumns )
            );  

            return null;
        }

        $record = array_combine( $currentColumnMap , $lineColumns );

        $this->adjustRecord( $record );

        return $record;
    }  

    protected function getFeedIdFromRecord ( $lineColumns ) {
        return $lineColumns[ 0 ];
    }

    protected function adjustRecord ( &$record ) {
        $record[ 'realtime' ] = 1;
        $record[ 'file' ] = $this->currentFile[ 'path' ];
        $record[ 'party' ] = $this->feedService->getPartyFromId( $record[ 'feed_id' ] );

        if ( '' === $record[ 'party' ] ) {
            $record[ 'party' ] = 0;
        }

        if ( !isset( $record[ 'source_url' ] ) || $record[ 'source_url' ] == '' ) {
            $record[ 'source_url' ] = $this->feedService->getSourceUrlFromId( $record[ 'feed_id' ] );
        }

        $record[ 'source_url' ] = ( strlen( $record[ 'source_url' ] ) > 255 ) ? substr( $record[ 'source_url' ] , 0 , 254 ) : $record[ 'source_url' ];

        if ( isset( $record[ 'dob' ] ) && $record[ 'dob' ] == '0000-00-00' ) {
            unset( $record[ 'dob' ] );
        } 
    }

    protected function logFailure ( $errors , $record , $file , $lineNumber , $email , $feedId ) {
        return $this->rawRepo->logBatchRealtimeFailure(
            $errors ,
            $record ,
            $file ,
            $lineNumber ,
            $email ,
            $feedId
        );
    }

    protected function columnMatchCheck ( $lineColumns ) {
        return true;
    }

    protected function isValidRecord ( $record , $rawRecord , $lineNumber ) {
        $valid = parent::isValidRecord( $record , $rawRecord , $lineNumber );

        if ( !isset( $this->recordCounts[ $record[ 'feed_id' ] ] ) ) {
            $this->recordCounts[ $record[ 'feed_id' ] ] = [
                "timestamp" => Carbon::now()->toDayDateTimeString() ,
                "feedName" => $this->feedService->getFeedNameFromId( $record[ 'feed_id' ] ) ,

                "party" => $this->feedService->getPartyFromId( $record[ 'feed_id' ] ) , 
                "success" => 0 ,
                "fail" => 0
            ];
        }

        if ( $valid ) {
            $this->recordCounts[ $record[ 'feed_id' ] ][ 'success' ]++;
        } else {
            $this->recordCounts[ $record[ 'feed_id' ] ][ 'fail' ]++;
        }

        return $valid;
    }

    protected function logProcessingComplete () {
        Notify::log( 'batch_feed' . $this->logKeySuffix , json_encode( [ "counts" => $this->recordCounts ] ) );
    }

    protected function logMissingFieldMapping () {
        #not needed, mapping is static.
    }

    protected function addFileToNotificationCollection () {
        #not needed, many files are small counts. doing aggregate instead
    }
}
