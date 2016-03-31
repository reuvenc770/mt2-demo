<?php

namespace App\Console\Traits;

use App\Models\JobEntry;
use App\Repositories\JobEntryRepo;

trait PreventOverlapping {

    protected function isRunning($name) {
        $model = new JobEntry();
        $repo = new JobEntryRepo($model);
        return $repo->alreadyRunning($name);
    }
}