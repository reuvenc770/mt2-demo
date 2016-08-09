<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\Attribution;

use App\Models\AttributionRecordReport;
use DB;

class RecordReportRepo {
    protected $report;

    public function __construct ( AttributionRecordReport $report ) {
        $this->report = $report;
    }

    public function getByDateRange ( array $dateRange ) {
        return $this->report->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] )->get();
    }

    public function runInsertQuery ( $valuesSqlString ) {
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

    public function massInsertActions ( $massData ) {
        $insertList = [];

        foreach ( $massData as $row ) {
            $insertList []= $this->mapData( $row );
        }

        $insertString = implode( ',' , $insertList );

        $this->runAccumulativeQuery( $insertString );
    }

    public function insertAction ( $actionData ) {
        $insertString = $this->mapData( $actionData );

        $this->runAccumulativeQuery( $insertString );
    }

    public function mapData ( $row ) {
        return "(
            '{$row[ 'email_id' ]}' ,
            '{$row[ 'deploy_id' ]}' ,
            0 ,
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

    public function runAccumulativeQuery ( $valueSqlString ) {
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_record_reports ( email_id , deploy_id , offer_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , date , created_at , updated_at )
            VALUES
                {$valueSqlString}
            ON DUPLICATE KEY UPDATE
                email_id = email_id ,
                deploy_id = deploy_id ,
                offer_id = offer_id ,
                delivered = IFNULL( delivered , 0 ) + IFNULL( VALUES( delivered ) , 0 ) ,
                opened = IFNULL( opened , 0 ) + IFNULL( VALUES( opened ) , 0 ) ,
                clicked = IFNULL( clicked , 0 ) + IFNULL( VALUES( clicked ) , 0 ) ,
                converted = IFNULL( converted , 0 ) + IFNULL( VALUES( converted ) , 0 ) ,
                bounced = IFNULL( bounced , 0 ) + IFNULL( VALUES( bounced ) , 0 ) ,
                unsubbed = IFNULL( unsubbed , 0 ) + IFNULL( VALUES( unsubbed ) , 0 ) ,
                revenue = IFNULL( revenue , 0.00 ) + IFNULL( VALUES( revenue ) , 0.00 ) ,
                date = date ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }
}
