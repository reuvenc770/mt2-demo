<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use DB;
use App\Repositories\AttributionLevelRepo;
use App\Models\AttributionLevel;
use App\Models\AttributionModel;

use Log;

class AttributionModelRepo {
    protected $models;

    public function __construct ( AttributionModel $models ) {
        $this->models = $models;
    }

    public function getModel () {
        return $this->models;
    }

    public function create ( $name , $levels = null ) {
        $response = [ "status" => false ];

        #creates new AttributionModel record.
        $newModel = new AttributionModel();
        $newModel->name = $name;
        $newModel->save();
            
        #generates temp level table
        AttributionLevelRepo::generateTempTable( $newModel->id );

        if ( !is_null( $levels ) ) {
            foreach ( $levels as $currentLevel ) {
                $tempLevelModel = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $newModel->id );
                $tempLevelModel->client_id = $currentLevel[ 'id' ];
                $tempLevelModel->level = $currentLevel[ 'level' ];
                $tempLevelModel->save();

                unset( $tempLevelModel );
            }
        } 

        $response[ 'status' ] = true;

        return $response;
    }

    public function get ( $modelId ) {
        return $this->models->where( 'id' , $modelId )->get();
    }

    public function toggleLiveStatus ( $modelId , $isLive ) {
        #throws exception if $isLive is not a boolean
        
        #resets all models to paused(false)
        #sets the given model to live status
    }

    public function levels ( $modelId ) {
        $modelLevelTable = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $modelId );

        return $modelLevelTable->get();
    }

    #sort by level
    public function getModelClients ( $modelId ) {
        return DB::connection( 'attribution' )
            ->table( AttributionLevel::BASE_TABLE_NAME . $modelId . ' AS al' )
            ->select( 'al.client_id as id' , 'c.name as name' )
            ->leftJoin( 'homestead.clients AS c' , 'c.id' , '=' , 'al.client_id' )
            ->orderBy( 'al.level' )
            ->get();
    }

    public function copyLevels ( $currentModelId , $templateModelId ) {
        $templateModelLevel = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $templateModelId );

        DB::connection( 'attribution' )->table( AttributionLevel::BASE_TABLE_NAME . $currentModelId )->truncate();

        $templateModelLevel->get()->each( function ( $item , $key ) use ( $currentModelId ) {
            $newLevel = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $currentModelId );

            $newLevel->client_id = $item->client_id;
            $newLevel->level = $item->level;
            $newLevel->save();

            unset( $newLevel );
        } );

        return true;
    }

    public function updateModel ( $currentModelId , $currentModelName , $levels ) {
        $currentModel = $this->models->find( $currentModelId );
        $currentModel->name = $currentModelName;
        $currentModel->save();

        DB::connection( 'attribution' )->table( AttributionLevel::BASE_TABLE_NAME . $currentModelId )->truncate();
        
        foreach ( $levels as $current ) {
            $newLevel = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $currentModelId );

            $newLevel->client_id = $current[ 'id' ];
            $newLevel->level = $current[ 'level' ];
            $newLevel->save();

            unset( $newLevel );
        }
    }

    public function transientRecords ( $modelId ) {
        #returns the current transient IDs
    }
}
