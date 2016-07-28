<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\Attribution;

use DB;
use Carbon\Carbon;

use App\Services\Attribution\AbstractReportAggregatorService;

use App\Services\EmailActionService;
use App\Services\CakeConversionService;

use App\Models\Email;
use App\Models\Suppression;
use App\Models\StandardReport;
use App\Models\AttributionRecordReport;

use App\Exceptions\RecordReportCollectionException;

class RecordAggregatorService extends AbstractReportAggregatorService {
    protected $conversionService;
    protected $action;

    public function __construct ( CakeConversionService $conversionService , EmailActionService $action ) {
        $this->conversionService = $conversionService;
        $this->action = $action;
    }

    public function buildAndSaveReport ( array $dateRange = null ) {
        if ( !isset( $this->action ) ) {
            throw new RecordReportCollectionException( 'EmailActionService is required. Please inject a service.' );
        }

        $this->setDateRange( $dateRange );

        $this->buildRecords();
        $this->loadRevenue();
        $this->loadSuppressions();
        $this->flattenStruct( 3 );
        $this->saveReport();
    }

    protected function getBaseRecords () {
        return $this->action->getByDateRange( $this->dateRange );
    }

    protected function processBaseRecord ( $baseRecord ) {
        $emailId = $baseRecord->email_id;
        $deployId = $baseRecord->deploy_id;
        $date = Carbon::parse( $baseRecord->datetime )->toDateString();

        $this->createRowIfMissing( $date , $emailId , $deployId );

        $currentRow = &$this->getCurrentRow( $date , $emailId , $deployId ); 

        switch ( $baseRecord->action_id ) {
            case 1 :
                $currentRow[ 'opened' ]++;
            break;

            case 2 :
                $currentRow[ 'clicked' ]++;
            break;

            case 3 :
                $currentRow[ 'converted' ]++;
            break;

            case 4 :
                $currentRow[ 'delivered' ]++;
            break;
        }
    }

    protected function loadRevenue ( $dateRange = null ) {
        $this->setDateRange( $dateRange );

        $conversions = $this->conversionService->getByDate( $this->dateRange ); 

        foreach ( $conversions as $current ) {
            $currentRow = &$this->getCurrentRow( Carbon::parse( $current->conversion_date )->toDateString() , $current->email_id , $current->deploy_id ); 

            $wholeRevenue = $currentRow[ 'revenue' ] * parent::WHOLE_NUMBER_MODIFIER;
            $conversionRevenue = $current->revenue * parent::WHOLE_NUMBER_MODIFIER;

            $currentRow[ 'revenue' ] = ( $wholeRevenue + $conversionRevenue ) / parent::WHOLE_NUMBER_MODIFIER;
        }
    }

    protected function loadSuppressions ( $dateRange = null ) {
        $this->setDateRange( $dateRange );

        $suppressions = Suppression::select( 'email_address' , 'esp_internal_id' , 'type_id' , 'date' )
            ->whereBetween( 'date' , [ $this->dateRange[ 'start' ] , $this->dateRange[ 'end' ] ] )
            ->get();

        foreach ( $suppressions as $currentSuppression ) {
            $emailId = $this->getEmailId( $currentSuppression->email_address ); 
            $deployId = $this->getDeployId( $currentSuppression->esp_internal_id ); 
            $date = $currentSuppression->date;

            $this->createRowIfMissing( $date , $emailId , $deployId );

            $currentRow = &$this->getCurrentRow( $date , $emailId , $deployId ); 

            switch ( $currentSuppression->type_id ) {
                case Suppression::TYPE_UNSUB:
                    $currentRow[ "unsubbed" ]++;
                break;

                case Suppression::TYPE_HARD_BOUNCE:
                    $currentRow[ "bounced" ]++;
                break;
            }
        }
    }

    protected function formatRecordToSqlString ( $record ) {
        return "( 
            '{$record[ 'email_id' ]}' ,
            '{$record[ 'deploy_id' ]}' ,
            '{$record[ 'offer_id' ]}' ,
            '{$record[ 'delivered' ]}' ,
            '{$record[ 'opened' ]}' ,
            '{$record[ 'clicked' ]}' ,
            '{$record[ 'converted' ]}' ,
            '{$record[ 'bounced' ]}' ,
            '{$record[ 'unsubbed' ]}' ,
            '{$record[ 'revenue' ]}' ,
            '{$record[ 'date' ]}' ,
            NOW() ,
            NOW()
        )";
    }

    protected function runInsertQuery ( $valuesSqlString ) {
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_record_reports ( email_id , deploy_id , offer_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
            ON DUPLICATE KEY UPDATE
                email_id = email_id ,
                deploy_id = deploy_id ,
                offer_id = offer_id ,
                delivered = VALUES( delivered ) ,
                opened = VALUES( opened ) ,
                clicked = VALUES( clicked ) ,
                converted = VALUES( converted ) ,
                bounced = VALUES( bounced ) ,
                unsubbed = VALUES( unsubbed ) ,
                revenue = VALUES( revenue ) ,
                date = date ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }

    protected function createRowIfMissing ( $date , $emailId , $deployId , $offerId = null ) {
        if ( is_null( $offerId ) ) { $offerId = parent::STATIC_OFFER_ID_PLACEHOLDER; }

        if ( !isset( $this->recordStruct[ $date ][ $emailId ][ $deployId ][ $offerId ] ) ) {
            $this->recordStruct[ $date ][ $emailId ][ $deployId ][ $offerId ] = [
                "email_id" => $emailId ,
                "deploy_id" => $deployId ,
                "offer_id" => $offerId ,
                "delivered" => 0 ,
                "opened" => 0 ,
                "clicked" => 0 ,
                "converted" => 0 ,
                "bounced" => 0 ,
                "unsubbed" => 0 ,
                "revenue" => 0.00 ,
                "date" => $date 
            ];
        }
    }

    protected function &getCurrentRow ( $date , $emailId , $deployId , $offerId = null ) {
        if ( is_null( $offerId ) ) { $offerId = parent::STATIC_OFFER_ID_PLACEHOLDER; }

        return $this->recordStruct[ $date ][ $emailId ][ $deployId ][ $offerId ];
    }

    protected function getEmailId ( $emailAddress ) {
        return Email::where( 'email_address' , $emailAddress )->pluck( 'id' )->pop();
    }

    protected function getDeployId ( $internalId ) {
        return StandardReport::where( "esp_internal_id" , $internalId )->first()->pluck( 'id' )->pop();
    }
}
