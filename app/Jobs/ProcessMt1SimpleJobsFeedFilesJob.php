<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\ProcessMt1RealtimeFeedFilesJob;

class ProcessMt1SimpleJobsFeedFilesJob extends ProcessMt1RealtimeFeedFilesJob { 
    protected $jobName = 'ProcessMt1SimpleJobsFeedFilesJob-';
    protected $serviceName = '\\App\\Services\\Mt1SimpleJobsProcessingService';
    protected $folderName = 'mt2_simplyjobs';
    protected $archiveDir = '/var/local/programdata/done/mt2_simplyjobs_archive/';

    public function __contruct ( $tracking , $runtimeThreshold="15m" ) {
        parent::__construct( $tracking , $runtimeThreshold );
    }
}
