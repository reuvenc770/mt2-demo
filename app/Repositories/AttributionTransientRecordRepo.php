<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionTransientRecord;
use Illuminate\Database\Schema\Blueprint;

class AttributionTransientRecordRepo {
    const BASE_TABLE_NAME = 'attribution_transient_records_model_';

    protected $records;

    public function __construct ( $attributionModelId ) {
        $this->records = new AttributionTransientRecord( self::BASE_TABLE_NAME . $attributionModelId );
    }

    public function getByClientId ( $clientId , $daysBack ) {
        #returns stats for given client ID
    }

    public function getByDeployId ( $deployId , $daysBack ) {
        #returns stats for given deploy ID
    }

    public function getByDaysBack ( $daysBack ) {
        #returns stats for given days back
    }

    static public function generateTempTable ( $modelId ) {
        Schema::create( self::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->increments('id');
            $table->integer( 'client_id' );
            $table->integer( 'deploy_id' );
            $table->integer( 'delivered' )->default( 0 );
            $table->integer( 'opens' )->default( 0 );
            $table->integer( 'clicks' )->default( 0 );
            $table->integer( 'conversions' )->default( 0 );
            $table->integer( 'bounces' )->default( 0 );
            $table->integer( 'unsubs' )->default( 0 );
            $table->decimal( 'rev' , 11 , 3 )->default( 0.000 );
            $table->decimal( 'cost' , 9 , 3 )->default( 0.000 );
            $table->decimal( 'ecpm' , 7 , 3 )->default( 0.000 );
            $table->integer( 'days_back' );
            $table->timestamps();

            $table->index( 'client_id' );
            $table->index( 'deploy_id' );
            $table->index( 'days_back' );
        });
    }

    static public function dropTempTable ( $modelId ) { 
        Schema::drop( self::BASE_TABLE_NAME . $modelId );
    }
}
