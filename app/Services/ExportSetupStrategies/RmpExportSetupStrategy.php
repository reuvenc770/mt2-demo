<?php

namespace App\Services\ExportSetupStrategies;

use App\Repositories\EspWorkflowLogRepo;
use App\Repositories\EspDataExportRepo;

class RmpExportSetupStrategy extends AbstractExportSetupStrategy {
    protected $mailableDomains = [];
    
    public function __construct(EspWorkflowLogRepo $workflowLogRepo, EspDataExportRepo $exportRepo, $feedId) {
        parent::__construct($workflowLogRepo, $exportRepo, $feedId);
    }

    public function getTargetId(ProcessingRecord $record) {
        return $this->defaultTargetList;
    }
}