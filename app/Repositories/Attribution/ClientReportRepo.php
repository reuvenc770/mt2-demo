<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\Attribution;

use DB;
use App\Models\AttributionClientReport;

class ClientReportRepo {
    protected $model;

    public function __construct ( AttributionClientReport $model ) {
        $this->model = $model;
    }

    public function getByDateRange ( array $dateRange ) {
        return $this->model->whereBetween( 'date' , [ $dateRange[ 'start' ] , $dateRange[ 'end' ] ] )->get();
    }

    public function runInsertQuery ( $valuesSqlString ) {
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_client_reports ( client_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , cost , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
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
