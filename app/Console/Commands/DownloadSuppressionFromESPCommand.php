<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\DownloadSuppressionFromESP;
use App\Repositories\EspApiAccountRepo;
use Illuminate\Foundation\Bus\DispatchesJobs;
class DownloadSuppressionFromESPCommand extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suppression:downloadESP {espName} {lookBack} {queueName?}';
    protected $espRepo;
    protected $lookBack;
    protected $job_id;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download suppression records from specified ESP going back specified number of days.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EspApiAccountRepo $espRepo)
    {
        parent::__construct();
        $this->espRepo = $espRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->lookBack = $this->argument('lookBack') ? $this->argument('lookBack') : config('jobs.defaultLookback');
        $queue = (string) $this->argument('queueName') ? $this->argument('queueName') : "default";
        $espName = $this->argument('espName');

        $espAccounts = $this->espRepo->getAccountsByESPName($espName);

        foreach ($espAccounts as $account){
            if($account->enable_suppression) {
                $this->job_id = str_random(16);
                $espLogLine = "{$account->name}::{$account->account_name}::$this->job_id";
                $this->info($espLogLine);
                $job = (new DownloadSuppressionFromESP($account->name, $account->id, $this->lookBack, $this->job_id))->onQueue($queue);
                $this->dispatch($job);
            }
        }
    }

    public function getJobId(){
        return $this->job_id;
    }
}
