<?php

namespace App\Console\Commands;


use App\Repositories\EspApiAccountRepo;
use Carbon\Carbon;
use App\Jobs\RetrieveApiReports;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
class GrabApiEspReports extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadApi {espName} {lookBack?} {queueName?} {espAccountId?}';
    protected $espRepo;
    protected $lookBack;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


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
        $this->lookBack = $this->argument('lookBack') ? $this->argument('lookBack') : env('LOOKBACK',5);
        $queue = (string) $this->argument('queueName') ? $this->argument('queueName') : "default";
        $espAccountId = $this->argument('espAccountId');
        $date = Carbon::now()->subDay($this->lookBack)->startOfDay()->toDateString();
        $espName = $this->argument('espName');

        $espAccounts = $this->espRepo->getAccountsByESPName($espName);
        if($espAccountId) {
            $espLogLine = "{$espName}::{$espAccountId}";
            $this->info($espLogLine);
            $job = (new RetrieveApiReports($espName, $espAccountId, $date, str_random(16)))->onQueue($queue);
            $this->dispatch($job);
        } else{
            foreach ($espAccounts as $account) {
                $espLogLine = "{$account->name}::{$account->account_name}";
                $this->info($espLogLine);
                $job = (new RetrieveApiReports($account->name, $account->id, $date, str_random(16)))->onQueue($queue);
                $this->dispatch($job);
            }
        }
    }
}
