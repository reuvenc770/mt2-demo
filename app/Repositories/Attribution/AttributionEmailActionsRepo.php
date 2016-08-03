<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\EmailAction;
use App\Repositories\EmailActionsRepo;

class AttributionEmailActionsRepo extends EmailActionsRepo {
    public function __construct ( EmailAction $actions ) {
        parent::__construct( $actions );
    }

    public function maxId () {
        return $this->actions
            ->orderBy('datetime', 'desc')
            ->orderBy('id', 'desc')
            ->first()['id'];    
    }

    public function nextNRows ( $start , $offset ) {
        return $this->actions
            ->where('id', '>=', $start)
            ->orderBy('datetime')
            ->orderBy('id')
            ->skip($offset)
            ->first()['id'];
    }

    public function pullAggregatedActions ( $startPoint , $endPoint ) {
        return DB::connection( 'reporting_data' ) ->select(
            "SELECT
                DATE( datetime ) AS `date` ,
                email_id ,
                deploy_id ,
                SUM( IF ( action_id = 4 , 1 , 0 ) ) AS `delivered`,
                SUM( IF( action_id = 1 , 1 , 0 ) ) AS `opened` ,
                SUM( IF( action_id = 2 , 1 , 0 ) ) AS `clicked`
            FROM
                email_actions
            WHERE
                id BETWEEN :start AND :end 
            GROUP BY
                email_id ,
                deploy_id ,
                `date`"
            , [
                ':start' => $startPoint ,
                ':end' => $endPoint
            ] );
    }
}
