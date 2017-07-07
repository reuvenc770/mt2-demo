<?php

namespace App\Console\Commands;

use App\Repositories\OrphanEmailRepo;
use Illuminate\Console\Command;
use DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\AdoptOrphanEmails as Orphanage;

class AdoptOrphanEmails extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:adoptOrphans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Processes Orphan Email Actions for required data. Available options: maxOrphans, chunkSize, queueName, chunkDelay, order';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(OrphanEmailRepo $repo)
    {
        $start = $repo->getMinId();
        $end = $repo->getMaxId();

        while ($start < $end) {
            $segmentSize = 5000;
            $segmentEnd = $repo->nextNRows($start, $segmentSize);
            $segmentEnd = $segmentEnd ?: $end;

            $orphans = $repo->getOrphansBetweenIds($start, $segmentEnd);
            $job = new Orphanage($orphans, str_random(16));
            $job->onQueue('orphanage');
            $this->dispatch($job);

            $start = $segmentEnd;
        }

    }
}
