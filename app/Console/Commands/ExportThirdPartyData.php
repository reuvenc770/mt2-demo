<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Traits\PreventOverlapping;
use App\Repositories\EtlPickupRepo;
use App\Jobs\ExportRecordsJob;
class ExportThirdPartyData extends Command
{

    use DispatchesJobs, PreventOverlapping;
    const NAME_BASE = 'FeedProcessing';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:exportThirdParty {feedId}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle() {
        $feedId = $this->argument('feedId');

        $job = new ExportRecordsJob($feedId, str_random(16));
        $this->dispatch($job);
    }
}
