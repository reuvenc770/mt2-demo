<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Commands\ProcessFeedRecords;
use App\Repositories\EspWorkflowRepo;

class GetFirstPartyRecords extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:firstParty';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute calls for first party record processing.';

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
    public function handle(EspWorkflowRepo $repo) {
        $feeds = $repo->getActiveWorkflowFeeds();

        foreach($feeds as $feed) {
            $this->call('feedRecords:process', [
                'party' => 1,
                '--feed' => $feed->feed_id
            ]);
        }
    }
}
