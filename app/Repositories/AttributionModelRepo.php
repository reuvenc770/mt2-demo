<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

use App\Models\AttributionModel;
use DB;

class AttributionModelRepo {
    protected $models;

    public function __construct ( AttributionModel $models ) {
        $this->models = $models;
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
