<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\ProcessMt1RealtimeFeedFilesJob;

class ProcessMt1UnemploymentFeedFilesJob extends ProcessMt1RealtimeFeedFilesJob { 
    protected $jobName = 'ProcessMt1UnemploymentFeedFilesJob-';
    protected $serviceName = '\\App\\Services\\Mt1UnemploymentProcessingService';
    protected $folderName = 'mt2_unemployment';
    protected $archiveDir = '/var/local/programdata/done/mt2_unemployment_archive/';

    public function __contruct ( $tracking , $runtimeThreshold="15m" ) {
        parent::__construct( $tracking , $runtimeThreshold );
    }
}
