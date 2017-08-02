<?php
/**
 * @author Adam Chin <achin>
 */

namespace App\Services;

use App\Services\Mt1RealtimeProcessingService;
use App\Services\FeedService;
use App\Services\DomainGroupService;
use App\Services\RemoteLinuxSystemService;
use App\Repositories\RawFeedEmailRepo;

use League\Csv\Reader;
use Carbon\Carbon;

class Mt1FoodstampsProcessingService extends Mt1RealtimeProcessingService {
    protected $serviceName = 'Mt1FoodstampsProcessingService';
    protected $logKeySuffix = '_realtime_foodstamps';

    protected $realtimeDirectory = '/var/local/programdata/done/mt2_foodstamps';
    protected $realtimeFeedId = 3016;
    protected $realtimeFileColumnMap = [
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
        'client_date' ,
        'misc' ,
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

    protected function extractData ( $csvLine ) {
        $reader = Reader::createFromString( trim( $csvLine ) );
        $reader->setDelimiter( '|' );

        $columns = $reader->fetchOne();

        array_push( $columns , \Carbon\Carbon::now()->toDateTimeString() );

        return $columns;
    }

    protected function getFeedIdFromRecord ( $lineColumns ) {
        return $this->realtimeFeedId;
    }

    protected function adjustRecord ( &$record ) {
        $record[ 'feed_id' ] = $this->realtimeFeedId;

        parent::adjustRecord( $record );
    }
}
