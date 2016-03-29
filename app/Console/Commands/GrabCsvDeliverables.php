<?php

namespace App\Console\Commands;

use App\Repositories\EspApiAccountRepo;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\RetrieveDeliverableCsvReports;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Storage;
class GrabCsvDeliverables extends Command {
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:csvDeliverables {espName}';
    protected $factory;
    protected $espRepo;
    protected $currentAction;
    protected $actions = array('delivered', 'opens', 'clicks');

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(EspApiAccountRepo $espRepo) {
        parent::__construct();
        $this->espRepo = $espRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $date = Carbon::now()->subDay(3)->toDateString();
        $espName = $this->argument('espName');

        $espAccounts = $this->espRepo->getAccountsByESPName($espName);

        // Do we want a job for each espaccount-action-file?

        foreach ($espAccounts as $account) {
            $accName = $account->account_name;
            $accountId = $account->id;
            $espId = $account->esp_id;

            foreach ($this->actions as $id => $action) {
                $location = "./$accName/$action";
                echo $location . ": ";
                $files = Storage::Files($location);
                $this->info(sizeof($files) . " files");

                foreach ($files as $file){
                    $fileInfo = pathinfo($file);
                    $filePath = storage_path() . '/app/' . $file;
                    $this->dispatch(new RetrieveDeliverableCsvReports($espId, $espName, $accountId, $action, $filePath, str_random(16)));
                }
            }
        }

    }

}