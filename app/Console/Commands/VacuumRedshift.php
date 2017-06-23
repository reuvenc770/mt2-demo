<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\RedshiftRepositories\ListProfileFlatTableRepo;
use App\Jobs\VacuumRedshiftJob;
class VacuumRedshift extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize tables in Redshift';

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
    public function handle(ListProfileFlatTableRepo $repo) {
        $job = new VacuumRedshiftJob($repo, str_random(16));
        $this->dispatch($job);
    }
}
