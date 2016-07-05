<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Repositories\AttributionLevelRepo;
use App\Repositories\AttributionTransientRecordRepo;
use App\Models\AttributionLevel;
use App\Models\AttributionTransientRecord;
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

    public function create ( $name , $levels = null , $templateModelId = null ) {
        $response = [ "status" => false ];

        Log::info( $levels );

        #creates new AttributionModel record.
        $newModel = new AttributionModel();
        $newModel->name = $name;
        $newModel->save();
            
        #generates temp level table
        AttributionLevelRepo::generateTempTable( $newModel->id );
        $newModel->attribution_level_table = AttributionLevel::BASE_TABLE_NAME . $newModel->id;
        $newModel->save();

        if ( !is_null( $levels ) ) {
            foreach ( $levels as $currentLevel ) {
                Log::info( $currentLevel );

                $tempLevelModel = new AttributionLevel( AttributionLevel::BASE_TABLE_NAME . $newModel->id );
                $tempLevelModel->client_id = $currentLevel[ 'id' ];
                $tempLevelModel->level = $currentLevel[ 'level' ];
                $tempLevelModel->save();

                unset( $tempLevelModel );
            }
        } 

        #generates temp transient record table
        AttributionTransientRecordRepo::generateTempTable( $newModel->id );
        $newModel->transient_records_table = AttributionTransientRecord::BASE_TABLE_NAME . $newModel->id;
        $newModel->save();
        
        #injects levels if template model ID present

        $response[ 'status' ] = true;

        return $response;
    }

    public function toggleLiveStatus ( $modelId , $isLive ) {
        #throws exception if $isLive is not a boolean
        
        #resets all models to paused(false)
        #sets the given model to live status
    }

    public function levels ( $modelId ) {
        #returns client attribution levels
    }

    public function transientRecords ( $modelId ) {
        #returns the current transient IDs
    }
}
