<?php

namespace App\Services\ExportSetupStrategies;

use App\Repositories\EspWorkflowLogRepo;
use App\Repositories\EspDataExportRepo;

class NullExportSetupStrategy extends AbstractExportSetupStrategy {
    protected $exportRepo;
    protected $workflowLogRepo;
    
    public function __construct(EspWorkflowLogRepo $workflowLogRepo, EspDataExportRepo $exportRepo) {
        $this->workflowLogRepo = $workflowLogRepo;
        $this->exportRepo = $exportRepo;
    }

    public function canExport(ProcessingRecord $record) {
        return true;
    }

    abstract public function getTargetId(ProcessingRecord $record) {
        return null;
    }
}