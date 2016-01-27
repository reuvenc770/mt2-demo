<?php

namespace App\Console\Commands;

use App\Factories\APIFactory;
use App\Repositories\EspAccountRepo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\RetrieveAPIReports;
use Illuminate\Foundation\Bus\DispatchesJobs;
class GrabApiEspReports extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadESP {espName}';
    protected $factory;
    protected $espRepo;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * GrabESPStats constructor.
     * @param  $factory
     */
    public function __construct(EspAccountRepo $espRepo)
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
        $date = Carbon::now()->subDay(5)->toDateString();
        $espName = $this->argument('espName');
        $espAccounts = $this->espRepo->getAccountsByEspName($espName);
        foreach ($espAccounts as $accounts){
            $espLogLine = "{$espName}::{$accounts->account_number}";
            $this->info($espLogLine);
            $this->dispatch(new RetrieveApiReports($espName, $accounts->account_number, $date));
        }
    }
}
