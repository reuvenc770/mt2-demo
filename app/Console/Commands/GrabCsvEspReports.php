<?php

//TODO Finish once have more info
namespace App\Console\Commands;

use App\Repositories\EspApiAccountRepo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\RetrieveCsvReports;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Storage;
class GrabCsvEspReports extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadCsv {espName}';
    protected $factory;
    protected $espRepo;

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

        $accounts = Storage::directories();
            foreach ($accounts as $account){
                $this->info("Starting {$account}");
                $espAccount = $this->espRepo->getEspInfoByAccountName($account);
                $campaignFiles = Storage::files($account."/campaigns");
                foreach($campaignFiles as $campaignFile){
                    $this->dispatch(new RetrieveCsvReports($espAccount->esp->name, $account, $campaignFile, str_random(16)));
                }

            }

       // }
    }
}
