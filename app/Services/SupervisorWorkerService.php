<?php

namespace App\Services;

class SupervisorWorkerService {
    
    private $supervisor;
    private $workerGroups = [];
    private $processInfo = [];

    private $programInfo = [];
    private $queueProgramMap = [];

    const RUNNING_STATES = ['STARTING', 'RUNNING', 'BACKOFF', 'STOPPING', 'EXITED'];
    const FAILED_STATES = ['FATAL', 'UNKNOWN'];

    public function __construct(\Supervisor\Supervisor $supervisor) {
        $this->supervisor = $supervisor;
        $this->queueProgramMap = config('supervisor.queueProgramMap');
        $this->programInfo = config('supervisor.program');

        $processInfo = $supervisor->getAllProcessInfo();
        $this->workerGroups = $this->getProgramInfo($processInfo);
        $this->processInfo = $this->buildInternalProcessInfo($processInfo);
    }

    public function getWorkerGroups() {
        return $this->workerGroups;
    }

    private function getProgramInfo($processInfo) {
        // list of objects
        $output = [];

        foreach ($processInfo as $process) {
            $group = $process['group'];
            $queueName = $this->programInfo[$group]['queueName']; 
            
            if (!isset($output[$queueName])) {
                $output[$queueName] = (object)[
                    'totalWorkers' => 0,
                    'activeWorkers' => 0,
                    'stoppedWorkers' => 0,
                    'canModify' => $this->programInfo[$group]['canModify']
                ];
            }

            $output[$queueName]->totalWorkers++;

            if ($this->isRunning($process)) {
                $output[$queueName]->activeWorkers++;
            }
            else {
                $output[$queueName]->stoppedWorkers++;
            }

            if ($this->hasFailed($process)) {
                $this->restartProcess($process);
            }

        }

        return $output;
    }

    private function buildInternalProcessInfo($processInfo) {
        $output = [];

        foreach ($processInfo as $process) {
            $group = $process['group'];

            if (!isset($output[$group])) {
                $output[$group] = [];
            }

            $data = [
                'processName' => $process['name'],
                'isRunning' => $this->isRunning($process)
            ];

            $output[$group][] = $data;
        }

        return $output;
    }


    public function getWorkerStats($queueName) {
        return $this->workerGroups[$queueName];
    }


    public function turnOnQueueWorkers($queueName, $count = null) {
        $groupName = $this->queueProgramMap[$queueName];

        if (null === $count) {
            $this->supervisor->startProcessGroup($groupName);
        }
        else {
            // turn on $count number of workers
            $i = 0;

            foreach ($this->processInfo[$groupName] as $process) {
                if ($i >= $count) {
                    break;
                }
                
                if (!$process['isRunning']) {
                    $this->supervisor->startProcess($groupName . ':' . $process['processName']);
                    $i++;
                }
            }

        }
    }


    public function turnOffQueueWorkers($queueName) {
        // All or nothing
        $groupName = $this->queueProgramMap[$queueName];
        $this->supervisor->stopProcessGroup($groupName);
    }


    private function isRunning(array $process) {
        $state = $process['statename'];
        return in_array($state, self::RUNNING_STATES);
    }


    private function hasFailed(array $process) {
        $state = $process['statename'];
        return in_array($state, self::FAILED_STATES);
    }


    private function restartProcess(array $process) {
        $processName = $process['group'] . ':' . $process['name'];
        $this->supervisor->stopProcess($processName);
        $this->supervisor->startProcess($processName);
    }


}