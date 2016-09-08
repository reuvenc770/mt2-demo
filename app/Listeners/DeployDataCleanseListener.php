<?php

namespace App\Listeners;

use App\Events\AttributionFileUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\NewDeployWasCreated;
use Log;
class DeployDataCleanseListener {
    
    public function __construct() {}
  
    public function handle (NewDeployWasCreated $event) {
    
        $deploy = $event->getDeployId();
       Log::info($deploy);
    }
}