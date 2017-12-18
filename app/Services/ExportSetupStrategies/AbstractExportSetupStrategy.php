<?php

namespace App\Services\ExportSetupStrategies;

use App\Repositories\EspWorkflowLogRepo;
use App\Repositories\EspDataExportRepo;

abstract class AbstractExportSetupStrategy {

    protected $exportRepo;
    protected $workflowLogRepo;
    const MINUTES_IN_DAY = 1440;

    protected $exportLocked;
    protected $fifteenMinuteLimit = null;
    protected $dayLimit = null;
    protected $batchTotal = 0;
    protected $mailableDomains = [];

    
    public function __construct(EspWorkflowLogRepo $workflowLogRepo, EspDataExportRepo $exportRepo, $feedId) {
        $this->workflowLogRepo = $workflowLogRepo;
        $this->exportRepo = $exportRepo;

        $exportData = $exportRepo->getExport($feedId);
        $this->currentFifteenMinuteCount = $exportRepo->getLastMinuteCount(15);
        $this->currentDayCount = $exportRepo->getLastMinuteCount(self::MINUTES_IN_DAY);

        $this->defaultTargetList = $exportData->target_list;
        $this->exportLocked = $exportData->is_locked;
        $this->fifteenMinuteLimit = $exportData->fifteen_minute_limit;
        $this->dayLimit = $exportData->day_limit;
    }

    public function canExport(ProcessingRecord $record) {
        // returns boolean based off of locking and domain rules

        $emailDomain = $this->getEmailDomain();

        if ($this->isMailableDomain($record->emailDomain)) {
            if (!$this->exportLocked) {
                if (is_null($this->fifteenMinuteLimit) && is_null($this->dayLimit)) {
                    // counts don't matter here, so nothing to increment
                    return true;
                }
                elseif (!is_null($this->fifteenMinuteLimit) && (1 + $this->batchTotal + $this->currentFifteenMinuteCount) <= $this->fifteenMinuteLimit) {
                    return true;
                }
                elseif (!is_null($this->dayLimit) && (1 + $this->batchTotal + $this->daycurrentDayCount) <= $this->dayLimit) {
                    return true;
                }
            }
        }

        return false;
    }
   
    
    /*
    Use the method below to set what list it should go to based off of hard-coded rules
    Example:

    if ($record->emailDomain === 'yahoo.com') {
        return 'alternate_target_list';
    }
    else {
        // default list
        return $this->defaultTargetList;
    }
    */

    abstract public function getTargetId(ProcessingRecord $record);

    public function incrementBatchTotal() {
        $this->batchTotal++;
    }

    protected function isMailableDomain($emailDomain) {
        if (empty($this->mailableDomains)) {
            return true;
        }
        else {
            $domain = strtolower($emailDomain);
            return in_array($domain, $this->mailableDomains);
        }
    }
}
