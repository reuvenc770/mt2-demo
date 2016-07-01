<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\AttributionModel;

class AttributionModelRepo {
    protected $models;

    public function __construct ( AttributionModel $models ) {
        $this->models = $models;
    }

    public function getModel () {
        return $this->models;
    }

    public function create ( $name , $templateModelId = null ) {
        #generates temp level table
        #generates temp transient record table
        
        #creates new AttributionModel record.
        
        #injects levels if template model ID present
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
