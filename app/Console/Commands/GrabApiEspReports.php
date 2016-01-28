<?php

namespace App\Console\Commands;


use App\Repositories\ESPAccountRepo;
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
    protected $signature = 'reports:downloadApi {espName} {lookBack?}';
    protected $espRepo;
    protected $lookBack;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    public function __construct(ESPAccountRepo $espRepo)
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

        $date = Carbon::now()->subDay($this->lookBack)->toDateString();
        $espName = $this->argument('espName');

        $espAccounts = $this->espRepo->getAccountsByESPName($espName);

        foreach ($espAccounts as $account){
            $espLogLine = "{$account->name}::{$account->account_name}";
            $this->info($espLogLine);
            $this->dispatch(new RetrieveApiReports($account->name, $account->id, $date, str_random(16)));
        }
    }
}
