<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\ProcessMt1RealtimeFeedFilesJob;

class ProcessMt1MedicaidFeedFilesJob extends ProcessMt1RealtimeFeedFilesJob { 
    protected $jobName = 'ProcessMt1MedicaidFeedFilesJob-';
    protected $serviceName = '\\App\\Services\\Mt1MedicaidProcessingService';
    protected $folderName = 'mt2_medicaid';
    protected $archiveDir = '/var/local/programdata/done/mt2_medicaid_archive/';

    public function __contruct ( $tracking , $runtimeThreshold="15m" ) {
        parent::__construct( $tracking , $runtimeThreshold );
    }
}
