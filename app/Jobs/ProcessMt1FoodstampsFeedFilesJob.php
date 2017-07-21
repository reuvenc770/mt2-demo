<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\ProcessMt1RealtimeFeedFilesJob;

class ProcessMt1FoodstampsFeedFilesJob extends ProcessMt1RealtimeFeedFilesJob { 
    protected $jobName = 'ProcessMt1FoodstampsFeedFilesJob-';
    protected $serviceName = '\\App\\Services\\Mt1FoodstampsProcessingService';
    protected $folderName = 'mt2_foodstamps';
    protected $archiveDir = '/var/local/programdata/done/mt2_foodstamps_archive/';

    public function __contruct ( $tracking , $runtimeThreshold="15m" ) {
        parent::__construct( $tracking , $runtimeThreshold );
    }
}
