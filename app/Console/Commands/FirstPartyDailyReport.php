<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EspWorkflowRepo;
use App\Repositories\EspWorkflowLogRepo;
use Carbon\Carbon;

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
    public function handle(EspWorkflowRepo $repo, EspWorkflowLogRepo $logRepo) {
        // Build out something like a report card ... need to get all active 1st party feeds

        $yesterday = Carbon::yesterday()->toDateString();
        $lists = $logRepo->getActiveLists($yesterday);

        // need lists here instead

        foreach($lists as $list) {
            $data = $logRepo->getDataForDate($list->target_list, $yesterday);
            $mtdCount = $logRepo->monthToDateCount($list->target_list, $yesterday);

            // So, how to handle when the target list belongs to a group?
            // Is this why we had workflow earlier?

            // Different data streams can use the same workflow
            // 
            So is it for each workflow for each ... stream?

            No.

            There are many streams. Many can belong to a workflow.

            So yes, this is for each workflow for each stream.
            What does a stream consist of?
            - BelongTo a workflow
            - 
        }

        Notify::sendData($data);
    }
}


        // 1. total count for date
        // 2. 
        /*
Foodstamps Count
Users Received from Foodstamps: 3450
Users Sent To Foodstamps List - 3413
Dupe - 23
Total Duplicates who have signed up 3X or More - 1
Users Received Month to Date from Foodstamps: 71121
---------------------------------------------------
HealthPlansOfAmerica Counts
Users Received from 0bce03ec00000000000000000000000fe75d: 416
Users Received from 0bce03ec000000000000000000000010cb84: 157
Users Received from 0bce03ec000000000000000000000010cb85: 262
Users Sent To Bronto by Status
    0bce03ec00000000000000000000000fe75d - Created - 353
    0bce03ec00000000000000000000000fe75d - Duplicate - 63
    0bce03ec000000000000000000000010cb84 - Created - 132
    0bce03ec000000000000000000000010cb84 - Duplicate - 25
    0bce03ec000000000000000000000010cb85 - Created - 209
    0bce03ec000000000000000000000010cb85 - Duplicate - 52
    0bce03ec000000000000000000000010cb85 - Other Error - 1
Users Received Month To Date from 0bce03ec00000000000000000000000fe75d: 23559
Users Received Month To Date from 0bce03ec000000000000000000000010cb84: 9818
Users Received Month To Date from 0bce03ec000000000000000000000010cb85: 10070

        */