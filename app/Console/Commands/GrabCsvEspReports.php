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
    protected $signature = 'reports:downloadCsv';
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

        $files = Storage::files("campaigns");
            foreach ($files as $file){
                if($file == "campaigns/.gitkeep"){
                    continue;
                }

                $pieces = explode('_',$file);
                $date = trim(explode('/',$pieces[0])[1]);
                $realDate = Carbon::createFromFormat('Ymd', $date)->startOfDay()->toDateTimeString();
                $account = explode('.',$pieces[1])[0];
                $this->info("Starting {$account}");
                    $this->dispatch(new RetrieveCsvReports($account, $file, $realDate, str_random(16)));
                }

    }
}
