<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Reports;

use Illuminate\Support\Collection;
use DB;
use Carbon\Carbon;
use App\Models\AttributionClientReport;
use App\Exceptions\ClientReportCollectionException;

class ClientReportCollection extends Collection {
    const WHOLE_NUMBER_MODIFIER = 100000;

    protected $recordReport;
    protected $emailClientService;
    protected $dateRonge;

    protected $clientStruct = [];

    public function __construct ( $items = [] ) {
        parent::__construct( $items );
    }

    public function load ( $dateRange = null ) {
        $this->setDateRange( $dateRange );

        parent::__construct( AttributionClientReport::whereBetween( 'date' , [ $this->dateRange[ 'start' ] , $this->dateRange[ 'end' ] ] )->get()->toArray() );
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

    public function injectEmailClientAssignmentService ( \App\Services\EmailClientAssignmentService $emailClientService ) {
        $this->emailClientService = $emailClientService;
    }

    public function buildAndSaveReport ( $dateRange = null ) {
        if ( !isset( $this->recordReport ) ) {
            throw new ClientReportCollectionException( 'RecordReport Model needed. Please inject a model.' );
        }

        if ( !isset( $this->emailClientService ) ) {
            throw new ClientReportCollectionException( 'EmailClientAssignmentService needed. Please inject a service.' );
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
            $clientId = $this->emailClientService->getAssignedClient( $current->email_id );
            
            $this->createRowIfMissing( $date , $clientId );

            $currentRow = &$this->getCurrentRow( $date , $clientId );

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
                        '{$row[ 'client_id' ]}' ,
                        '{$row[ 'delivered' ]}' ,
                        '{$row[ 'opened' ]}' ,
                        '{$row[ 'clicked' ]}' ,
                        '{$row[ 'converted' ]}' ,
                        '{$row[ 'bounced' ]}' ,
                        '{$row[ 'unsubbed' ]}' ,
                        '{$row[ 'revenue' ]}' ,
                        '{$row[ 'cost' ]}' ,
                        '{$row[ 'date' ]}' ,
                        NOW() ,
                        NOW()
                    )";
                }

                $insertString = implode( ',' , $rowStringList );

                DB::connection( 'attribution' )->insert( "
                    INSERT INTO
                        attribution_client_reports ( client_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , cost , date , created_at , updated_at )
                    VALUES
                        {$insertString}
                    ON DUPLICATE KEY UPDATE
                        client_id = client_id ,
                        delivered = VALUES( delivered ) ,
                        opened = VALUES( opened ) ,
                        clicked = VALUES( clicked ) ,
                        converted = VALUES( converted ) ,
                        bounced = VALUES( bounced ) ,
                        unsubbed = VALUES( unsubbed ) ,
                        revenue = VALUES( revenue ) ,
                        cost = VALUES( cost ) ,
                        date = date ,
                        created_at = created_at ,
                        updated_at = NOW()
                " );
            }
        }
    }

    protected function createRowIfMissing ( $date , $clientId ) {
        if ( !isset( $this->recordStruct[ $date ][ $clientId ] ) ) {
            $this->recordStruct[ $date ][ $clientId ] = [
                "client_id" => $clientId ,
                "delivered" => 0 ,
                "opened" => 0 ,
                "clicked" => 0 ,
                "converted" => 0 ,
                "bounced" => 0 ,
                "unsubbed" => 0 ,
                "revenue" => 0 ,
                "cost" => 0.00 ,
                "date" => $date
            ];
        }
    }

    protected function &getCurrentRow ( $date , $clientId ) {
        return $this->recordStruct[ $date ][ $clientId ];
    }
}
