<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EtlPickupRepo;
use App\Jobs\PopulateAttributionMappingJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class PopulateMappingTable extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:map';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Store MT1 <-> CMP attribution mapping information';

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
    public function handle(EtlPickupRepo $repo) {
        $job = new PopulateAttributionMappingJob($repo, str_random(16));
        $this->dispatch($job);
    }
}
