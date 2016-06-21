<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ExportActionsJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\EspApiAccountRepo;
use Carbon\Carbon;

class ExportActionsElsewhere extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export {reportName} {esp} {espAccount?} {--lookback=} {--queue=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export data.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(EspApiAccountRepo $espAccountRepo) {
        $reportName = $this->argument('reportName');
        $esp = $this->argument('esp');
        $espAccount = $this->argument('espAccount') ? $this->argument('espAccount') : 'all';
        $espAccounts = $espAccount === 'all' ? 
            $espAccountRepo->getAccountsByESPName($esp) : [$espAccountRepo->getEspInfoByAccountName($espAccount)];

        $lookback = $this->option('lookback') ? $this->option('lookback') : env('LOOKBACK',5);
        $date = Carbon::now()->subDay($lookback)->startOfDay()->toDateString();

        $queue = $this->option('queue') ? $this->option('queue') : 'default';

        $job = (new ExportActionsJob($reportName, $esp, $espAccounts, $date, str_random(16)))->onQueue($queue);
        $this->dispatch($job);
    }
}
