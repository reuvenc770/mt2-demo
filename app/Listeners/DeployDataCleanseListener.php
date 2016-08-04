<?php

namespace App\Listeners;

use App\Events\AttributionFileUploaded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Repositories\MT1Repositories\Mt1UniqueProfileRepo;
use App\Events\NewDeployWasCreated;

class DeployDataCleanseListener {
  
    private $uniqueProfileRepo;
  
    public function __construct(Mt1UniqueProfileRepo $uniqueProfileRepo) {
        $this->uniqueProfileRepo = $uniqueProfileRepo;
    }
  
    public function handle (NewDeployWasCreated $event) {
    
        $deploy = $event->getDeploy();
        $segments = $deploy->getSegments();
        
        foreach ($segments as $segment) {
            $this->uniqueProfileRepo->setPull($segment->getId());

            Artisan::call('the ftp dispatch job', ['segmentId' => $segment->getId()]);
        }    
    
    }
}