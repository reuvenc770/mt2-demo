<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\CpaReportingListProfile;
use App\Models\DeploySnapshot;
use App\Repositories\DeploySnapshotRepo;

#MtOfferCakeOfferMapping

class CpaListProfileReportRepo {
    protected $model;
    protected $snapshot;

    public function __construct ( CpaReportingListProfile $model , DeploySnapshotRepo $snapshot ) {
        $this->model = $model;
        $this->snapshot = $snapshot;
    }

    public function getFeedRecordDistribution ( $cakeOfferId ) {
        return $this->snapshot->getFeedRecordDistribution( $cakeOfferId );
    }

    public function massInsert ( $recordStringList ) {
        $recordSqlString = implode( ' , ' , $recordStringList );
        $reportDb = config( 'database.connections.reporting_data.database' );

        \DB::insert( "INSERT INTO
            {$reportDb}.cpa_reporting_list_profile( feed_id , cake_offer_id , offer_id , deploy_id , conversions , rev , created_at , updated_at )
        VALUES
            {$recordSqlString}
        ON DUPLICATE KEY UPDATE
            feed_id = feed_id ,
            cake_offer_id = cake_offer_id ,
            offer_id = offer_id ,
            deploy_id = deploy_id ,
            conversions = VALUES( conversions ) ,
            rev = VALUES( rev ) ,
            created_at = created_at ,
            updated_at = NOW()
        " );
    }

    public function toSqlFormat ( $record ) {
        $pdo = \DB::connection()->getPdo();

        return '('
            . $pdo->quote( $record[ 'feed_id' ] ) . ','
            . $pdo->quote( $record[ 'cake_offer_id' ] ) . ','
            . $pdo->quote( $record[ 'offer_id' ] ) . ','
            . $pdo->quote( $record[ 'deploy_id' ] ) . ','
            . $pdo->quote( $record[ 'conversions' ] ) . ','
            . $pdo->quote( $record[ 'rev' ] ) . ','
            . "NOW(),NOW()" 
        . ')';
    }
}
