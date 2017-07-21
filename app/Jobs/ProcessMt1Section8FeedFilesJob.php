<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Jobs;

use App\Jobs\ProcessMt1RealtimeFeedFilesJob;

class ProcessMt1Section8FeedFilesJob extends ProcessMt1RealtimeFeedFilesJob { 
    protected $jobName = 'ProcessMt1Section8FeedFilesJob-';
    protected $serviceName = '\\App\\Services\\Mt1Section8ProcessingService';
    protected $folderName = 'mt2_hosting';
    protected $archiveDir = '/var/local/programdata/done/mt2_hosting_archive/';

    public function __contruct ( $tracking , $runtimeThreshold="15m" ) {
        parent::__construct( $tracking , $runtimeThreshold );
    }
}
