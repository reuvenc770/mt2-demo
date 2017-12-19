<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\PullEmailsJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;

class PullEmails extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emails:Pull {feedId} {startdate} {enddate} {--runtime-threshold=default}';
    protected $feedId;
    protected $startdate;
    protected $enddate;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
	$this->feedId = explode(",",$this->argument( 'feedId' ));
        $runtime = $this->option('runtime-threshold');
        $tracking = str_random(16);
        $this->startdate = $this->argument( 'startdate' );
	$this->enddate = $this->argument( 'enddate' );
        $job = new PullEmailsJob($this->feedId, $this->startdate, $this->enddate, $tracking,$runtime);
        $this->dispatch($job);
    }
}
