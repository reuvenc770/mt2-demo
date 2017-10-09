<?php

namespace App\Jobs;

use App\Factories\ServiceFactory;

class OptimizeWorkersJob extends MonitoredJob {

    public function __construct($runtimeThreshold, $tracking) {
        $jobName = 'OptimizeWorkers';
        parent::__construct($jobName, $runtimeThreshold, $tracking);
    }

    protected function handleJob() {
        $supervisorService = ServiceFactory::createSupervisorService();
        $queueService = ServiceFactory::createQueueService();

        $workerGroups = $supervisorService->getWorkerGroups();

        foreach ($workerGroups as $queueName => $workerStats) {

            if (!$workerStats->canModify) {
                // Prevent certain worker groups from being modified
                continue;
            }

            $queue = $queueService->getQueueInfo($queueName);
            $potentialJobs = $queue->activeJobs + $queue->queuedJobs;

            if ($potentialJobs > 0) {
                // Jobs to run
                $runningWorkers = $workerStats->activeWorkers;
                $totalAvailableWorkers = $workerStats->totalWorkers;
                $stoppedWorkers = $workerStats->stoppedWorkers;

                if ($runningWorkers === $totalAvailableWorkers) {
                    // Queue all turned on. Nothing to do here.
                    continue;
                }
                else {
                    $workerCount = min($potentialJobs, $stoppedWorkers);
                    $supervisorService->turnOnQueueWorkers($queue->name, $workerCount);
                }
            }
            else {
                // No jobs to run
                if (0 === $workerStats->activeWorkers) {
                    // all good - no jobs, no workers
                    continue;
                }
                else {
                    // Unneeded workers. Disable the entire group.
                    $supervisorService->turnOffQueueWorkers($queue->name);
                }
            }

        }
    
    }
}