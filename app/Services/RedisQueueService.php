<?php

namespace App\Services;

class RedisQueueService {
    private $redis;
    private $queues;

    public function __construct(\Predis\Client $redis) {
        $this->redis = $redis;
        $this->queues = $this->getQueues();
    }

    public function getQueues() {
        $output = [];
        $queues = $this->redis->keys('queues:*');

        foreach ($queues as $queueName) {
            $items = explode(':', $queueName);
            $name = $items[1];

            if (!isset($output[$name])) {
                $output[$name] = (object)[
                    'name' => $name,
                    'activeJobs' => 0,
                    'queuedJobs' => 0 
                ];
            }

            if (sizeof($items) === 2) {
                // This is the reserve
                $output[$name]->queuedJobs += $this->getListLength($queueName);
            }
            elseif (sizeof($items) === 3 && 'reserved' === $items[2]) {
                $output[$name]->activeJobs += $this->getZSetLength($queueName);
            }
            elseif (sizeof($items) === 3 && 'delayed' === $items[2]) {
                $output[$name]->queuedJobs += $this->getZSetLength($queueName);
            }

        }

        return $output;
    }

    public function getQueueInfo($queueName) {
        if (isset($this->queues[$queueName])) {
            return $this->queues[$queueName];
        }
        else {
            // If they don't currently exist, they are empty
            return (object)[
                'name' => $queueName,
                'activeJobs' => 0,
                'queuedJobs' => 0
            ];
        }
    }

    private function getListLength($listName) {
        return $this->redis->llen($listName);
    }

    private function getZSetLength($setName) {
        return $this->redis->zcard($setName);
    }
}