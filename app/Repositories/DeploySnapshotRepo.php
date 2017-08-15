<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\DeploySnapshot;
use App\Models\Deploy;
use App\Models\ListProfileBaseTable;

class DeploySnapshotRepo {
    const BASE_LP_EXPORT_TABLE_NAME = 'export_';

    protected $model;

    public function __construct ( DeploySnapshot $model ) {
        $this->model = $model;
    }

    public function massInsert ( $recordStringList ) {
        $recordSqlString = implode( ' , ' , $recordStringList );
        $reportDb = config( 'database.connections.reporting_data.database' );

        \DB::insert( "INSERT INTO
            {$reportDb}.deploy_snapshots ( email_id , email_address , deploy_id , feed_id )
        VALUES
            {$recordSqlString}
        ON DUPLICATE KEY UPDATE
            email_id = email_id ,
            email_address = email_address ,
            deploy_id = deploy_id ,
            feed_id = VALUES( feed_id )
        " );
    }

    public function toSqlFormat ( $record ) {
        $pdo = \DB::connection()->getPdo();

        return '('
            . $pdo->quote( $record[ 'email_id' ] ) . ','
            . $pdo->quote( $record[ 'email_address' ] ) . ','
            . $pdo->quote( $record[ 'deploy_id' ] ) . ','
            . $pdo->quote( $record[ 'feed_id' ] )
        . ')';
    }

    public function clearForDeploy ( $deployId ) {
        return $this->model->where( 'deploy_id' , $deployId )->delete();
    }

    public function getListProfileExportsFromDeploy ( $deployId ) {
        $listProfiles = [];

        $deploy = Deploy::where( 'id' , '=' , $deployId )->with( 'listProfileCombine' )->first();

        if ( !is_null( $deploy ) ) {
            $listProfileCollection = $deploy->listProfileCombine->listProfiles()->get();

            foreach ( $listProfileCollection as $current ) {
                $listProfiles []= new ListProfileBaseTable( self::BASE_LP_EXPORT_TABLE_NAME . $current->id );
            }
        }

        return $listProfiles;
    }

    public function getFeedRecordDistribution ( $deployId ) {
        $reportSchema = config( 'database.connections.reporting_data.database' );

        return \DB::select( "SELECT
            COUNT( * ) / tc.total_count AS feed_perc ,
            feed_id
        FROM
            {$reportSchema}.deploy_snapshots ds
            INNER JOIN (
                SELECT
                    COUNT( * ) AS total_count ,
                    ds.deploy_id
                FROM
                    {$reportSchema}.deploy_snapshots ds
                WHERE
                    ds.deploy_id = {$deployId}
                GROUP BY
                    ds.deploy_id
            ) tc ON( ds.deploy_id = tc.deploy_id )
        WHERE
            ds.deploy_id = {$deployId}
        GROUP BY
            ds.feed_id ,
            tc.total_count;" );
    }
}
