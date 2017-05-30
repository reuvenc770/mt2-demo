<?php

namespace App\Console\Commands;
use App\Jobs\RunTimeMonitorJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Console\Command;
use Carbon\Carbon;

/**
 * Class RunTimeMonitorCommand
 * @package App\Console\Commands
 * dispatches RunTimeMonitorJob
 */
class RunTimeMonitorCommand extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     * --simulate=[0|1], signal sent to acceptance test simulation
     * --runtime_threshold=[0-9]{1,}(s|m|h|)  in seconds, minutes, or hours
     */
    protected $signature = 'monitors:runtime {--mode=} {--days-back=?} {--dt-start=?} {--dt-end=?} {--runtime-threshold=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'dispatches a RunTimeMonitorJob';

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
        $date1 = $this->option('days-back')!="?" ? $this->option('days-back') : $this->option('dt-start');
        $date2 = $this->option('dt-end')!="?" ? $this->option('dt-end') : null;

        $job = new RunTimeMonitorJob($this->option('mode'),$this->option('runtime-threshold'),$date1,$date2);
        $this->dispatch( $job->onQueue( 'Monitor' ) );
    }
}
