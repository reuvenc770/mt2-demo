<?php

namespace App\Jobs\Traits;

trait PreventJobOverlapping {

    protected $mutexPath;

    protected function createLock($name) {
        $this->mutexPath = $this->getMutexPath($name);
        return touch($this->mutexPath);
    }

    protected function getMutexPath($name) {
        return storage_path('framework/schedule-' . md5($name));
    }

    public function jobCanRun($name) {
        if (!file_exists($this->getMutexPath($name))) {
            // not currently running
            return true;
        }
        else {
            // already running
            return false;
        }
    }

    protected function unlock() {
        echo "running unlock ... " . PHP_EOL;
        $result = unlink($this->mutexPath);
        echo "RESULT: $result" . PHP_EOL;
    }
}