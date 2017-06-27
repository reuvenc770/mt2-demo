<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Jobs\DataProcessingJob;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Traits\PreventOverlapping;

class PopulateListProfileAggregationTable extends Command {
    use DispatchesJobs, PreventOverlapping;

    protected $signature = 'listprofile:aggregateActions {lookback?} {--runtime-threshold=}';
    protected $description = 'Populate the aggregation table for list profile action lookups';
    protected $lookback = 3;

    public function __construct() {
        parent::__construct();
    }

    public function handle() {
        $lookback = $this->argument('lookback') ?: $this->lookback;
        $this->dispatch(new DataProcessingJob('ListProfileAggregation', str_random(16), $lookback, $this->option('runtime-threshold')));
    }
}
