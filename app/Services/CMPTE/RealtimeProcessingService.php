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
use League\Csv\Reader;

class RealtimeProcessingService extends RemoteFeedFileService {
    protected $serviceName = 'RealtimeProcessingService';
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
        return [
            [ 'directory' => '/var/local/programdata/done/mt2_realtime' , 'feedId' => 0 ] ,
            [ 'directory' => '/var/local/programdata/done/mt2_foodstamps' , 'feedId' => 3016 ] ,
            [ 'directory' => '/var/local/programdata/done/mt2_hosting' , 'feedId' => 2961 ] ,
            [ 'directory' => '/var/local/programdata/done/mt2_medicaid' , 'feedId' => 3018 ] ,
            [ 'directory' => '/var/local/programdata/done/mt2_simplyjobs' , 'feedId' => 2983 ] ,
            [ 'directory' => '/var/local/programdata/done/mt2_unemployment' , 'feedId' => 3017 ] ,
        ];
    }

    protected function getFileColumnMap ( $feedId ) {
        if ( $this->isSimplyJobs() ) {
            return [
                'city' ,
                'email_address' ,
                'first_name' ,
                'last_name' ,
                'state' ,
                'source_url' ,
                'zip' ,
                'ip' ,
                'ts' ,
                'job_type' ,
                'gradyear' ,
                'salary' ,
                'educationlevel' ,
                'utm_source' ,
                'password' ,
                'sourceID' ,
                'capture_date' ,
            ];
        } elseif ( $this->isOtherFirstPartyFormat() ) {
            return [
                'address' ,
                'carowner' ,
                'city' ,
                'credit_score' ,
                'datetime_collected',
                'diabetes' ,
                'dob_day' ,
                'dob_month' ,
                'dob_year' ,
                'email_address' ,
                'first_name' ,
                'gender' ,
                'have_car_insurance' ,
                'have_personal_or_work_injury' ,
                'height_ft' ,
                'height_in' ,
                'in_debt' ,
                'in_debt_amount' ,
                'interested_education' ,
                'interested_faster_benefits_with_direct_deposit' ,
                'interested_going_solar' ,
                'interested_in_publishers_clearing_house' ,
                'interested_in_uber_driving' ,
                'interested_receiving_job_listing_by_email' ,
                'interested_speak_health_insurance_specialist' ,
                'interested_store_coupons' ,
                'last_name' ,
                'lead_code' ,
                'lead_id_token' ,
                'looking_for_job' ,
                'phone' ,
                'pregnant' ,
                'ss_or_disability' ,
                'state' ,
                'step' ,
                'tcpa_3' ,
                'source_url',
                'user_agent' ,
                'user_date_time' ,
                'ip',
                'utm_source',
                'weight_lbs' ,
                'zip',
                'sourceID' ,
                'capture_date' ,
            ];
        } else {
            /**
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
    }

    protected function isSpecificPathFeed ( $feedName ) {
        return ( strpos( $this->currentFile[ 'path' ] , $feedName ) !== false );
    }

    protected function isSimplyJobs () {
        return $this->isSpecificPathFeed ( 'simplyjobs' );
    }

    protected function isFoodStamps () {
        return $this->isSpecificPathFeed ( 'foodstamps' );
    }

    protected function isSection8 () {
        return $this->isSpecificPathFeed ( 'hosting' );
    }

    protected function isMedicaid () {
        return $this->isSpecificPathFeed ( 'medicaid' );
    }

    protected function isUnemployment () {
        return $this->isSpecificPathFeed ( 'unemployment' );
    }

    protected function isOtherFirstPartyFormat () {
        return ( $this->isFoodStamps() || $this->isSection8() || $this->isMedicaid() || $this->isUnemployment() );
    }

    protected function extractData ( $csvLine ) {
        if ( $this->isSimplyJobs() || $this->isOtherFirstPartyFormat() ) {
            $this->extractDataFirstParty( $csvLine );
        } else {
            $this->extractDataNormal( $csvLine );
        }
    }

    protected function extractDataFirstParty ( $csvLine ) {
        $reader = Reader::createFromString( trim( $csvLine ) );
        $reader->setDelimiter( '|' );

        $columns = $reader->fetchOne();

        array_push( $columns , \Carbon\Carbon::now()->toDateTimeString() );

        return $columns;
    }

    protected function extractDataNormal ( $csvLine ) {
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

        $record = array_combine( $currentColumnMap , $lineColumns );
        $record[ 'realtime' ] = 1;

        if ( $this->isSimplyJobs() || $this->isOtherFirstPartyFormat() ) { 
            $record[ 'party' ] = 1;
            $record[ 'feed_id' ] = $this->currentFile[ 'feedId' ];
        } else {
            $record[ 'party' ] = $this->feedService->getPartyFromId( $record[ 'feed_id' ] );
        }


        if ( $record[ 'dob' ] == '0000-00-00' ) {
            unset( $record[ 'dob' ] );
        } 

        return $record;
    }  

    protected function columnMatchCheck ( $lineColumns ) {
        return true;
    }
}
