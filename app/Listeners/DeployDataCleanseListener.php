<?php

namespace App\Listeners;

use App\Events\AttributionFileUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\NewDeployWasCreated;

class DeployDataCleanseListener {
    
    public function __construct() {}
  
    public function handle (NewDeployWasCreated $event) {
    
        $deploy = $event->getDeploy();
        $segments = $deploy->getSegments();
        
        foreach ($segments as $segment) {
            // we do something here
        }    
    
    }
}