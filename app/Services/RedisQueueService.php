<?php

namespace App\Services;

class RedisQueueService {
    private $redis;

    public function __construct(Predis\Client $redis) {
        $this->redis = $redis;
    }

    public function getQueues() {
        $output = [];
        $queues = $this->build($this->keys('queues:*'));

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
            elseif (sizeof($items) === 3 && 'reserved' === $item[2]) {
                $output[$name]->activeJobs += $this->getZSetLength($queueName);
            }
            elseif (sizeof($items) === 3 && 'delayed' === $item[2]) {
                $output[$name]->queuedJobs += $this->getZSetLength($queueName);
            }

        }

        return $output;
    }

    private function getListLength($listName) {
        return $this->redis->llen($listName);
    }

    private function getZSetLength($setName) {
        return $this->redis->zcard($setName);
    }
}