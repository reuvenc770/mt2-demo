<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\Attribution;

use DB;

class DeployAggregatorRepo { 
    public function __construct () {}

    public function runInsertQuery ( $valuesSqlString ) {
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_deploy_reports ( deploy_id , delivered , opened , clicked , converted , bounced , unsubbed , revenue , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
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
