<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services\CMPTE;

use App\Services\RemoteFeedFileService;
use Maknz\Slack\Facades\Slack;
use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;
use App\Models\ProcessedFeedFile;
use App\Repositories\RawFeedEmailRepo;
use App\Models\MT1Models\User as Feeds;

class RealtimeProcessingService extends RemoteFeedFileService {
    protected $slackChannel = '#cmp_hard_start_errors';
    protected $currentCustomFields = [];

    public function __construct ( FeedService $feedService , RemoteLinuxSystemService $systemService , DomainGroupService $domainGroupService , RawFeedEmailRepo $rawRepo ) {
        parent::__construct( $feedService , $systemService , $domainGroupService , $rawRepo );
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
        return [ [ 'directory' => '/var/local/programdata/done/mt2_realtime' , 'feedId' => 0 ] ];
    }

    protected function getFileColumnMap ( $feedId ) {
        /**
         * Mapping: $client_id,$email,$gender,$first_name,$last_name,$birth_date,$address,$address2,$city,$state,$zip,$country,$phone,$ip,$url,$domain_id,$cha_news,$customData
         *
         * Example: 3038|crqrtrkr@yahoo.com||Clarence Ray|Robinson||6414 Roxanne Dr||Fort Wayne|IN|46816||4192966104|70.198.65.55|http//www.bestmoneysearch.com|0|1|subid=14424-16778-501880;studentdebt=No;creditdebt=No;education=;
         */
        return [
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

    }

    protected function extractData ( $csvLine ) {
        /**
         * TODO:
         *
         * - Look for pipes in the custom field section at the end of the line. This will cause mismatching columns and values
         * - Look for 24 char strings where the ip should be and add colons. This is causing records to fail validation.
         */
        $columns = explode( '|' , $csvLine );

        #ipv6 fix, add colons
        if ( strlen( $columns[ 13 ] ) == 32 ) {
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
            $this->rawRepo->logBatchFailure(
                'Error when parsing line. Zipping columns with values failed.' ,
                json_encode( $lineColumns ) ,
                $this->currentFile[ 'path' ] , 
                0 ,
                '' , 
                $lineColumns[ 0 ]
            );  

            return null;
        }

        return array_combine( $currentColumnMap , $lineColumns );
    }

    protected function columnMatchCheck ( $lineColumns ) {
        return true;
    }
}
