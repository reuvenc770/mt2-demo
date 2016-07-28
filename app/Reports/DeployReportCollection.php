<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Reports;

use Illuminate\Support\Collection;
use DB;
use Carbon\Carbon;
use App\Models\AttributionDeployReport;
use App\Exceptions\DeployReportCollectionException;

class DeployReportCollection extends Collection {
    const WHOLE_NUMBER_MODIFIER = 100000;

    protected $recordReport;
    protected $dateRonge;

    protected $recordStruct = [];

    public function __construct ( $items = [] ) {
        parent::__construct( $items );
    }

    public function load ( $dateRange = null ) {
        $this->setDateRange( $dateRange );

        parent::__construct( AttributionDeployReport::whereBetween( 'date' , [ $this->dateRange[ 'start' ] , $this->dateRange[ 'end' ] ] )->get()->toArray() );
    }

    public function setDateRange ( $dateRange = null ) { 
        if ( is_null( $dateRange ) && !isset( $this->dateRange ) ) { 
            $this->dateRange = [ "start" => Carbon::today()->startOfDay()->toDateTimeString() , "end" => Carbon::today()->endOfDay()->ToDateTimeString() ];
        } elseif ( !is_null( $dateRange ) && isset( $this->dateRange ) ) { 
            $this->dateRange = $dateRange;
        }   
    } 

    public function injectRecordReportModel ( \App\Models\AttributionRecordReport $recordReport ) {
        $this->recordReport = $recordReport;
    }

    public function buildAndSaveReport ( $dateRange = null ) {
        if ( !isset( $this->recordReport ) ) {
            throw new DeployReportCollectionException( 'RecordReport Model needed. Please inject a model.' );
        }

        $this->setDateRange( $dateRange );

        $this->loadRecords();

        $this->buildReportRowsFromStruct();

        $this->saveRecords();
    }

    public function loadRecords ( $dateRange = null ) {
        $this->setDateRange( $dateRange );
        
        $records = $this->recordReport::whereBetween( 'date' , [ $this->dateRange[ 'start' ] , $this->dateRange[ 'end' ] ] )->get();

        foreach ( $records as $current ) {
            $date = $current->date;
            $deployId = $current->deploy_id;
            
            $this->createRowIfMissing( $date , $deployId );

            $currentRow = &$this->getCurrentRow( $date , $deployId );

            $currentRow[ "delivered" ] += $current[ "delivered" ];
            $currentRow[ "opened" ] += $current[ "opened" ];
            $currentRow[ "clicked" ] += $current[ "clicked" ];
            $currentRow[ "converted" ] += $current[ "converted" ];
            $currentRow[ "bounced" ] += $current[ "bounced" ];
            $currentRow[ "unsubbed" ] += $current[ "unsubbed" ];
            $currentRow[ "revenue" ] = ( $currentRow[ "revenue" ] * self::WHOLE_NUMBER_MODIFIER + $current[ "revenue" ] * self::WHOLE_NUMBER_MODIFIER ) / self::WHOLE_NUMBER_MODIFIER;
        }
    }

    protected function buildReportRowsFromStruct () {
        $this->items = collect( $this->recordStruct )->flatten( 1 )->all();
    }

    protected function saveRecords () {
        if ( $this->count() ) {
            $chunkedRows = $this->chunk( 10000 );

            foreach ( $chunkedRows as $chunk ) {
                $rowStringList = [];

                foreach ( $chunk as $row ) {
                    $rowStringList []= "( 
                        '{$row[ 'deploy_id' ]}' ,
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
                        attribution_deploy_reports ( deploy_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , date , created_at , updated_at )
                    VALUES
                        {$insertString}
                    ON DUPLICATE KEY UPDATE
                        deploy_id = deploy_id ,
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

    protected function createRowIfMissing ( $date , $deployId ) {
        if ( !isset( $this->recordStruct[ $date ][ $deployId ] ) ) {
            $this->recordStruct[ $date ][ $deployId ] = [
                "deploy_id" => $deployId ,
                "delivered" => 0 ,
                "opened" => 0 ,
                "clicked" => 0 ,
                "converted" => 0 ,
                "bounced" => 0 ,
                "unsubbed" => 0 ,
                "revenue" => 0 ,
                "date" => $date
            ];
        }
    }

    protected function &getCurrentRow ( $date , $deployId ) {
        return $this->recordStruct[ $date ][ $deployId ];
    }
}
