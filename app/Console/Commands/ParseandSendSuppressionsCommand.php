<?php

namespace App\Console\Commands;

use App\Jobs\ParseAndSendSuppressions;
use App\Repositories\EspApiAccountRepo;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Cache;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ParseandSendSuppressionsCommand extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movetoftp:suppressions {espName} {lookBack} {queueName?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $espRepo;
    protected $lookBack;
    protected $queue;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EspApiAccountRepo $espApiAccountRepo)
    {
        parent::__construct();
        $this->espRepo = $espApiAccountRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->lookBack = $this->argument('lookBack') ? $this->argument('lookBack') : env('LOOKBACK',5);
        $this->queue = (string) $this->argument('queueName') ? $this->argument('queueName') : "default";
        $date = Carbon::now()->subDay($this->lookBack)->startOfDay()->toDateString();
        $espName = $this->argument('espName');
        $espAccounts = $this->espRepo->getAccountsByESPName($espName);

        foreach ($espAccounts as $account){
            Cache::increment("{$account->name}_accounts_to_go",1);
            $espLogLine = "{$account->name}::{$account->account_name}";
            $this->info($espLogLine);
            $job = (new ParseAndSendSuppressions($espAccounts,$account->name, $account->account_name, $account->id, $date, str_random(16)))->onQueue($this->queue);
            $this->dispatch($job);
        }
    }
}
