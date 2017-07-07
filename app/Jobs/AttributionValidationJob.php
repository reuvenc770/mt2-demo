<?php

namespace App\Jobs;

use App\Models\JobEntry;
use App\Factories\DataProcessingFactory;
use App\Facades\JobTracking;

class AttributionValidationJob extends SafeJob {

    private $lookback;
    protected $jobName = 'AttributionValidation';
    private $startPoint;

    public function __construct($startPoint, $tracking) {
        $this->startPoint = $startPoint;
        parent::__construct($this->jobName, $tracking);
    }

    protected function handleJob() {
        $service = DataProcessingFactory::create($this->jobName);
        return $service->process($this->startPoint);
    }

}