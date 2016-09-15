<?php

namespace App\Listeners;

use App\Events\AttributionFileUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\NewDeployWasCreated;
use App\Factories\ServiceFactory;

use Log;

class PackageCreationListener {
    
    public function __construct() {}
  
    public function handle (NewDeployWasCreated $event) {
    
        $deployId = $event->getDeployId();
        
        $service = ServiceFactory::createPackageCreationService();

        $html = $service->createPackage($deployId);

        Log::info($html);
    }
}
