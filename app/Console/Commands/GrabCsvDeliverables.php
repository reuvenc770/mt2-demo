<?php

namespace App\Console\Commands;

use App\Models\EmailAction;
use App\Repositories\EspApiAccountRepo;
use Carbon\Carbon;
use App\Services\AbstractReportService;
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
    protected $signature = 'reports:csvDeliverables';
    protected $factory;
    protected $espRepo;
    protected $currentAction;
    protected $actions = array('delivered');

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

            $files = Storage::files("delivers");
            foreach ($files as $file){
                if($file == "campaigns/.gitkeep"){
                    continue;
                }

                $pieces = explode('_',$file);
                $date = trim(explode('/',$pieces[0])[1]);
                $realDate = Carbon::createFromFormat('Ymd', $date)->startOfDay()->toDateTimeString();
                $account = explode('.',$pieces[1])[0];
                $this->info("Starting {$account}");
                $this->dispatch(new RetrieveDeliverableCsvReports($account, $file, $realDate, AbstractReportService::RECORD_TYPE_DELIVERABLE, str_random(16)));
            }

        }



}

