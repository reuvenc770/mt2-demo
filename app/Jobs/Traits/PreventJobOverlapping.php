<?php

namespace App\Jobs\Traits;

trait PreventJobOverlapping {

    protected $mutexPath;

    protected function createLock($name) {
        $this->mutexPath = $this->getMutexPath($name);
        return touch($this->mutexPath);
    }

    protected function getMutexPath($name) {
        // Limited job name because they can be very long and we need the full thing for uniqueness.
        return storage_path('framework/schedule-' . substr($name, 0, 30) . '~' . md5($name));
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

    protected function unlock($name) {
        if ('' !== $this->mutexPath) {
            return unlink($this->mutexPath);
        }
        else {
            $path = $this->getMutexPath($name);
            return unlink($path);
        }
        
    }
}