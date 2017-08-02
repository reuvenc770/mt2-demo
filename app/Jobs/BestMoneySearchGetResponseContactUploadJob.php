<?php

namespace App\Jobs;
use App\Facades\JobTracking;

class BestMoneySearchGetResponseContactUploadJob extends MonitoredJob
{
    const BASE_JOB_NAME = 'BestMoneySearchGetResponseContactUploadJob';
    const ESP_ACCOUNT_NAME = 'GR002';
    const FEED_ID = 3038;
    const CAMPAIGN_ID = 'TGb8Q';

    protected $jobName;
    protected $dateRange;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $dateRange , $tracking , $runtimeThreshold )
    {
        $this->dateRange = $dateRange;
        $this->tracking = $tracking;
        $this->jobName = self::BASE_JOB_NAME . ":" . json_encode( $dateRange );

        parent::__construct( $this->jobName , $runtimeThreshold, $tracking );
    }

    public function handleJob()
    {
        $espApiRepo = \App::make( \App\Services\EspApiAccountService::class );
        $api = \App::make( \App\Services\API\GetResponseApi::class , [ $espApiRepo->getEspAccountIdFromName( self::ESP_ACCOUNT_NAME ) ] );
        $recordSource = \App::make( \App\Models\MT1Models\EmailList::class );

        $result = $recordSource->where( 'client_id' , self::FEED_ID )->whereBetween( 'lastUpdated' , [ $this->dateRange[ 'start' ] , $this->dateRange[ 'end' ] ] );
        \Log::info( $result->count() );

        if ( $result->count() > 0 ) {
            $recordCollection = $result->get();

            foreach ( $recordCollection as $record ) {
                $shouldUploadContact = !$api->contactExists( $record->email_addr );

                if ( $shouldUploadContact ) {
                    $response = $api->addContact( self::CAMPAIGN_ID , [
                        'id' => $record->email_user_id ,
                        'email' => $record->email_addr ,
                        'firstName' => $record->first_name ,
                        'lastName' => $record->last_name
                    ] );

                    \Log::info( $response );
                }
            }
        }
    }
}
