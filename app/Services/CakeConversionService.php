<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Services\Interfaces\IConversion;
use App\Repositories\CakeConversionRepo;
use App\Services\API\CakeConversionApi;
use App\Repositories\Attribution\RecordReportRepo;
use App\Jobs\AttributionConversionJob;
use Carbon\Carbon;

class CakeConversionService implements IConversion {
    protected $repo;
    protected $recordRepo;
    protected $api;

    public function __construct ( CakeConversionRepo $repo , CakeConversionApi $api , RecordReportRepo $recordRepo ) {
        $this->repo = $repo;
        $this->recordRepo = $recordRepo;
        $this->api = $api;
    }

    public function getByDate ( $dateRange = null ) {
        return $this->repo->getByDate( $dateRange );
    }

    public function getByDeployEmailDate ( $deployId , $emailId , $date  ) {
        return $this->repo->getByDeployEmailDate ( $deployId , $emailId , $date  );
    }

    public function updateConversionsFromAPI ( $processMode , $recordType , $date ) {
        if ( in_array( $recordType , [ 'cpc' , 'all' ] ) ) {
            $this->retrieveAndSaveFromAPI( $processMode , 'cpc' , $date );
        }

        if ( in_array( $recordType , [ 'cpa' , 'all' ] ) ) {
            $this->retrieveAndSaveFromAPI( $processMode , 'cpa' , $date );
        }
    }

    public function retrieveAndSaveFromAPI ( $processMode , $recordType , $date ) {
        $statsGuzzle = $this->api->sendApiRequest( [
            'recordType' => $recordType ,
            'start' => Carbon::parse( $date )->startOfDay()->toDateTimeString() ,
            'end' => Carbon::parse( $date )->endOfDay()->toDateTimeString()
        ] );

        $statsResponse = json_decode( $statsGuzzle->getBody()->getContents() );

        $sqlStringList = $this->formatSqlValueString( $processMode , $statsResponse->stats , $recordType );

        $this->repo->insertOrUpdate( $sqlStringList[ 'conversion' ] );

        if ( $processMode === AttributionConversionJob::PROCESS_MODE_REALTIME ) {
            $this->recordRepo->runAccumulativeQuery( $sqlStringList[ 'record' ] );
        }
    }

    protected function formatSqlValueString ( $processMode , $stats , $recordType ) {
        $conversionRows = [];
        $recordReportRows = [];

        foreach ( $stats as $currentRecord ) {
            $emailId = $this->parseEmailId( $currentRecord->s2 );

            $conversionRows []= '(' . implode( ',' , [
                $emailId ,
                "'{$currentRecord->s1}'" ,
                "'{$currentRecord->s2}'" ,
                "'{$currentRecord->s3}'" ,
                "'{$currentRecord->s4}'" ,
                "'{$currentRecord->s5}'" ,
                $currentRecord->click_id ,
                "'{$currentRecord->conversion_datetime}'" ,
                "'{$currentRecord->conversion_id}'" ,
                ( $recordType === 'cpc' ? 1 : 0 ) ,
                $currentRecord->req_id ,
                $currentRecord->affiliate_id ,
                $currentRecord->offer_id ,
                $currentRecord->advertiser_id ,
                $currentRecord->campaign_id ,
                $currentRecord->creative_id ,
                $currentRecord->received_raw ,
                $currentRecord->received_usa ,
                $currentRecord->paid_raw ,
                $currentRecord->paid_usa ,
                $currentRecord->paid_currency_id ,
                $currentRecord->received_currency_id ,
                ( $currentRecord->conversion_rate > 0 ? $currentRecord->conversion_rate : 1.00 ) ,
                "'{$currentRecord->ip}'" ,
                'NOW()' ,
                'NOW()'
            ] ) . ")";

            if ( $processMode === AttributionConversionJob::PROCESS_MODE_REALTIME ) {
                $recordReportRows []= '(' . implode( ',' , [
                    $emailId ,
                    "'{$currentRecord->s1}'" ,
                    $currentRecord->offer_id ,
                    $currentRecord->received_usa ,
                    "'{$currentRecord->conversion_datetime}'" ,
                    'NOW()' ,
                    'NOW()'
                ] ) . ')';
            }
        }

        $output = [ 'conversion' => implode( ',' , $conversionRows ) ];

        if ( $processMode === AttributionConversionJob::PROCESS_MODE_REALTIME ) {
            $output[ 'record' ] = implode( ',' , $recordReportRows );
        }

        return $output;
    }

    protected function parseEmailId ( $subId ) {
        $subIdParts = explode( '_' , $subId );

        if ( count( $subIdParts ) > 0 ) {
            $emailId = $subIdParts[ 0 ];

            if ( is_numeric( $emailId ) ) {
                return (int)$emailId;
            }
        }

        return 0;
    }
}
