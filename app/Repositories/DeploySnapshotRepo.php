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
            {$reportDb}.deploy_snapshots ( email_address , deploy_id , feed_id )
        VALUES
            {$recordSqlString}
        ON DUPLICATE KEY UPDATE
            email_address = email_address ,
            deploy_id = deploy_id ,
            feed_id = VALUES( feed_id )
        " );
    }

    public function toSqlFormat ( $record ) {
        $pdo = \DB::connection()->getPdo();

        return '('
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
}
