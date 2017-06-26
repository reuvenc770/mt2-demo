<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EspWorkflowRepo;
use App\Repositories\FeedDateEmailBreakdownRepo;

class FirstPartyDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

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
    public function handle(EspWorkflowRepo $repo, FeedDateEmailBreakdownRepo $statRepo) {
        // Build out something like a report card ... need to get all active 1st party feeds

        $firstPartyFeeds = $workflowRepo->getActiveWorkflows();

        foreach($firstPartyFeeds as $feed) {

            $data = $statRepo->

            Notify::sendData($data);
        }
    }
}
