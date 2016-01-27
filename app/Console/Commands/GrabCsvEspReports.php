<?php

namespace App\Console\Commands;

use App\Factories\APIFactory;
use App\Repositories\EspAccountRepo;
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
    protected $signature = 'reports:downloadCSV {espName}';
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
     * @param APIFactory $factory
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
        $files = Storage::Files($espName);  //most likely will be FTP or /s3
            foreach ($files as $file){
              $fileInfo = pathinfo($file);
                $this->info("Starting {$espName}");
                $this->dispatch(new RetrieveCsvReports($espName, "BH001", $file));
            }

       // }
    }
}
