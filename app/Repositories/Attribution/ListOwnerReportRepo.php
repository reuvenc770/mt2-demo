<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories\Attribution;

use DB;
use App\Models\AttributionListOwnerReport;

class ListOwnerReportRepo {
    protected $model;

    public function __construct ( AttributionListOwnerReport $model ) {
        $this->model = $model;
    }

    public function runInsertQuery ( $valuesSqlString ) {
        DB::connection( 'attribution' )->insert( "
            INSERT INTO
                attribution_list_owner_reports ( client_stats_grouping_id , standard_revenue , cpm_revenue , mt1_uniques , mt2_uniques , date , created_at , updated_at )
            VALUES
                {$valuesSqlString}
            ON DUPLICATE KEY UPDATE
                client_stats_grouping_id = client_stats_grouping_id ,
                standard_revenue = VALUES( standard_revenue ) ,
                cpm_revenue = VALUES( cpm_revenue ) ,
                mt1_uniques = VALUES( mt1_uniques ) ,
                mt2_uniques = VALUES( mt2_uniques ) ,
                date = date ,
                created_at = created_at ,
                updated_at = NOW()
        " );
    }
}
