<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;
use App\Jobs\SendSuppressionsToMT1 as SendSuppressionJob;
class SendSuppressionsToMT1 extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $lookBack;
    protected $signature = 'suppression:sendToMT1 {lookBack}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs all suppressions for  X Days and places them on the MT1 FTP';

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
        $this->lookBack = $this->argument('lookBack') ? $this->argument('lookBack') : config('jobs.defaultLookback');
        $date = Carbon::now()->subDay($this->lookBack)->startOfDay()->toDateString();
        $job = (new SendSuppressionJob($date, str_random(16)));
        $this->dispatch($job);
    }


}
