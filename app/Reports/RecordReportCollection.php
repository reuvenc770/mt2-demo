<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Reports;

use Illuminate\Support\Collection;
use DB;
use Carbon\Carbon;
use App\Models\Email;
use App\Models\Suppression;
use App\Models\StandardReport;
use App\Models\AttributionRecordReport;
use App\Exceptions\RecordReportCollectionException;

class RecordReportCollection extends Collection {
    const STATIC_OFFER_ID_PLACEHOLDER = 0;
    const WHOLE_NUMBER_MODIFIER = 100000;

    protected $action;
    protected $dateRange;
    protected $recordStruct = [];

    public function __construct ( $items = [] ) {
        parent::__construct( $items );
    }

    public function load ( $dateRange = null ) {
        $this->setDateRange( $dateRange );

        parent::__construct( AttributionRecordReport::whereBetween( 'date' , [ $this->dateRange[ 'start' ] , $this->dateRange[ 'end' ] ] )->get()->toArray() );
    }

    public function injectActionService ( \App\Services\EmailActionService $action ) {
        $this->action = $action;
    }

    public function setDateRange ( $dateRange = null ) {
        if ( is_null( $dateRange ) && !isset( $this->dateRange ) ) {
            $this->dateRange = [ "start" => Carbon::today()->startOfDay()->toDateTimeString() , "end" => Carbon::today()->endOfDay()->ToDateTimeString() ];
        } elseif ( !is_null( $dateRange ) && isset( $this->dateRange ) ) {
            $this->dateRange = $dateRange;
        }
    }

    public function buildAndSaveReport ( \App\Services\Interfaces\IConversion $conversionService , $dateRange = null ) {
        if ( !isset( $this->action ) ) {
            throw new RecordReportCollectionException( 'EmailActionService is required. Please inject a service.' );
        }

        $this->setDateRange( $dateRange );

        $this->loadRecords();
        $this->loadRevenue( $conversionService );
        $this->loadSuppressions();
        $this->buildReportRowsFromStruct();
        $this->saveReport();
    }

    protected function loadRecords ( $dateRange = null ) {
        $this->setDateRange( $dateRange );

        $records = $this->action->getByDateRange( $this->dateRange );

        foreach ( $records as $current ) {
            $emailId = $current->email_id;
            $deployId = $current->deploy_id;
            $date = Carbon::parse( $current->datetime )->toDateString();

            $this->createRowIfMissing( $date , $emailId , $deployId );

            $currentRow = &$this->getCurrentRow( $date , $emailId , $deployId ); 

            switch ( $current->action_id ) {
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
    }

    protected function loadRevenue ( \App\Services\Interfaces\IConversion $conversionService , $dateRange = null ) {
        $this->setDateRange( $dateRange );

        $conversions = $conversionService->getByDate( $this->dateRange ); 

        foreach ( $conversions as $current ) {
            $currentRow = &$this->getCurrentRow( Carbon::parse( $current->conversion_date )->toDateString() , $current->email_id , $current->deploy_id ); 

            $wholeRevenue = $currentRow[ 'revenue' ] * self::WHOLE_NUMBER_MODIFIER;
            $conversionRevenue = $current->revenue * self::WHOLE_NUMBER_MODIFIER;

            $currentRow[ 'revenue' ] = ( $wholeRevenue + $conversionRevenue ) / self::WHOLE_NUMBER_MODIFIER;
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

    protected function buildReportRowsFromStruct () {
        $this->items = collect( $this->recordStruct )->flatten( 3 )->all();
    }

    protected function saveReport () {
        if ( $this->count() ) {
            $chunkedRows = $this->chunk( 10000 );

            foreach ( $chunkedRows as $chunk ) {
                $rowStringList = [];

                foreach ( $chunk as $row ) {
                    $rowStringList []= "( 
                        '{$row[ 'email_id' ]}' ,
                        '{$row[ 'deploy_id' ]}' ,
                        '{$row[ 'offer_id' ]}' ,
                        '{$row[ 'delivered' ]}' ,
                        '{$row[ 'opened' ]}' ,
                        '{$row[ 'clicked' ]}' ,
                        '{$row[ 'converted' ]}' ,
                        '{$row[ 'bounced' ]}' ,
                        '{$row[ 'unsubbed' ]}' ,
                        '{$row[ 'revenue' ]}' ,
                        '{$row[ 'date' ]}' ,
                        NOW() ,
                        NOW()
                    )";
                }

                $insertString = implode( ',' , $rowStringList );

                DB::connection( 'attribution' )->insert( "
                    INSERT INTO
                        attribution_record_reports ( email_id , deploy_id , offer_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , date , created_at , updated_at )
                    VALUES
                        {$insertString}
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
        }
    }

    protected function createRowIfMissing ( $date , $emailId , $deployId , $offerId = RecordReportCollection::STATIC_OFFER_ID_PLACEHOLDER ) {
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

    protected function &getCurrentRow ( $date , $emailId , $deployId , $offerId = RecordReportCollection::STATIC_OFFER_ID_PLACEHOLDER ) {
        return $this->recordStruct[ $date ][ $emailId ][ $deployId ][ $offerId ];
    }

    protected function getEmailId ( $emailAddress ) {
        return Email::where( 'email_address' , $emailAddress )->pluck( 'id' )->pop();
    }

    protected function getDeployId ( $internalId ) {
        return StandardReport::where( "esp_internal_id" , $internalId )->first()->pluck( 'id' )->pop();
    }
}
