<?php

namespace App\Console\Commands;

use App\Jobs\AWeberActionImmigration;
use App\Repositories\AWeberEmailActionsStorageRepo;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;

class ProcessAWeberActions extends Command
{
    use DispatchesJobs;
    use InteractsWithQueue;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aweber:processAWeberActions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Try to bring AWeber back into the email_action process';

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
    public function handle(AWeberEmailActionsStorageRepo $repo) {
        $start = $repo->getMinId();
        $end = $repo->getMaxId();

        while ($start < $end) {
            $segmentSize = 5000;
            $segmentEnd = $repo->nextNRows($start, $segmentSize);
            $segmentEnd = $segmentEnd ?: $end;

            $segment = $repo->getOrphansBetweenIds($start, $segmentEnd);

            $this->info("Processing another chunk");
            $job = (new AWeberActionImmigration($segment, str_random(16)))->onQueue("AWeber");
            $this->dispatch($job);

            $start = $segmentEnd;
        }
    }
}
