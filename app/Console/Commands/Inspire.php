<?php

namespace App\Console\Commands;



use App\Jobs\ImportCsvStats;
use App\Models\ListProfileCombine;

use App\Repositories\ListProfileCombineRepo;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Inspire extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = storage_path() . "/app/files/uploads/csvuploads/20161118/upload.csv";
       $job = new ImportCsvStats("Campaigner",$path);
        $this->dispatch($job);
        }

}
