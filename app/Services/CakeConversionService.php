<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Services\Interfaces\IConversion;
use App\Repositories\CakeConversionRepo;
use App\Services\API\CakeConversionApi;
use Carbon\Carbon;
use Maknz\Slack\Facades\Slack;

class CakeConversionService implements IConversion {
    CONST ROOM = "#mt2-dev-failed-jobs";

    protected $repo;
    protected $api;

    public function __construct ( CakeConversionRepo $repo , CakeConversionApi $api ) {
        $this->repo = $repo;
        $this->api = $api;
    }

    public function getByDate ( $dateRange = null ) {
        return $this->repo->getByDate( $dateRange );
    }

    public function getByDeployEmailDate ( $deployId , $emailId , $date  ) {
        return $this->repo->getByDeployEmailDate ( $deployId , $emailId , $date  );
    }

    public function updateConversionsFromAPI ( $date ) {
        $this->retrieveAndSaveFromAPI( 'cpc' , $date );

        $this->retrieveAndSaveFromAPI( 'cpa' , $date );
    }

    public function retrieveAndSaveFromAPI ( $recordType , $date ) {
        $statsGuzzle = $this->api->sendApiRequest( [
            'recordType' => $recordType ,
            'start' => Carbon::parse( $date )->startOfDay()->toDateTimeString() ,
            'end' => Carbon::parse( $date )->endOfDay()->toDateTimeString()
        ] );

        $statsResponse = json_decode( $statsGuzzle->getBody()->getContents() );

        if ( count( $statsResponse->stats ) > 0 ) {
            $sqlStringList = $this->formatSqlValueString( $statsResponse->stats , $recordType );

            $this->repo->insertOrUpdate( $sqlStringList[ 'conversion' ] );
        } else {
            Slack::to(self::ROOM)->send( "Failed to grab conversions from CAKE. No data returned from service." );
        }
    }

    protected function formatSqlValueString ( $stats , $recordType ) {
        $conversionRows = [];

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
        }

        $output = [ 'conversion' => implode( ',' , $conversionRows ) ];

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
