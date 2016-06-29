<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionLevel;
use Illuminate\Database\Schema\Blueprint;

class AttributionLevelRepo {
    const BASE_TABLE_NAME = 'attribution_levels_model';

    protected $levels;

    public function __construct ( AttributionLevel $levels , $attributionModelId = null ) {
        $this->levels = $levels;

        if ( !is_null( $attributionModelId ) && is_numeric( $attributionModelId ) ) {
            $this->table = self::BASE_TABLE_NAME . $attributionModelId;
        }
    }

    public function setLevel ( $clientId , $level ) {
        #insert or update given client and level.
    }

    public function getLevel ( $clientId ) {
        #returns level for the given client.
    }

    public function getAllLevels () {
        #returns all levels
    }

    public function toggleActiveStatus ( $clientId , $isActive ) {
        #sets active filed for the given client.
    }

    public function generateTempTable ( $modelId ) {
        Schema::create( self::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->integer( 'client_id' )->unsigned();
            $table->integer( 'level' )->unsigned();
            $table->boolean( 'active' )->default( true );
            $table->timestamps();

            $table->primary( 'client_id' );
            $table->index( [ 'client_id' , 'level' ] );
        });
    }

    public function dropTempTable ( $modelId ) {
        Schema::drop( self::BASE_TABLE_NAME . $modelId );
    }
}
