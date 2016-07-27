<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionLevel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class AttributionLevelRepo {
    protected $levels;

    public function __construct ( $attributionModelId = null ) {
        if ( !is_null( $attributionModelId ) && is_numeric( $attributionModelId ) ) {
            $this->levels = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $attributionModelId );
        } else {
            $this->levels = new AttributionLevel();
        }
    }

    public function setLevel ( $clientId , $level ) {
        #insert or update given client and level.
    }

    public function getLevel ( $clientId ) {
        return $this->levels
             ->select('level')
             ->where('client_id', $clientId)
             ->get();
    }

    public function getAllLevels () {
        #returns all levels
    }

    public function toggleActiveStatus ( $clientId , $isActive ) {
        #sets active filed for the given client.
    }

    static public function generateTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->create( AttributionLevel::BASE_TABLE_NAME . $modelId , function (Blueprint $table) {
            $table->integer( 'client_id' )->unsigned();
            $table->integer( 'level' )->unsigned();
            $table->boolean( 'active' )->default( true );
            $table->timestamps();

            $table->primary( 'client_id' );
            $table->index( [ 'client_id' , 'level' ] );
        });
    }

    static public function dropTempTable ( $modelId ) {
        Schema::connection( 'attribution' )->drop( AttributionLevel::BASE_TABLE_NAME . $modelId );
    }
}
