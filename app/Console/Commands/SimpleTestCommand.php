<?php

namespace App\Console\Commands;
use App\Jobs\SimpleTestJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Console\Command;
use Carbon\Carbon;

/**
 * Class SimpleTestCommand
 * @package App\Console\Commands
 * dispatches SimpleTestJob
 */
class SimpleTestCommand extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     * --simulate=[0|1], signal sent to acceptance test simulation
     * --runtime-threshold=[0-9]{1,}(s|m|h|)  in seconds, minutes, or hours
     */
    protected $signature = 'tests:simpletestjob {--simulate=} {--runtime-threshold=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dispatches a SimpleTestJob';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $job = new SimpleTestJob($this->option('simulate'),$this->option('runtime-threshold'));
        $this->dispatch($job);
    }
}
