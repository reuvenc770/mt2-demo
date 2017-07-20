<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\EspWorkflowRepo;
use App\Repositories\EspDataExportRepo;
use App\Repositories\EspWorkflowLogRepo;
use Carbon\Carbon;
use Mail;

class FirstPartyDailyReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:dailyExportReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Send out report of yesterday's user exports";

    CONST LVL_MAIL = "alphateam@zetainteractive.com";
    const TECH_EMAIL = "tech.team.mt2@zetaglobal.com";

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
    public function handle(EspWorkflowRepo $repo, EspDataExportRepo $dataExportRepo, EspWorkflowLogRepo $logRepo) {
        // Build out an assoc array to populate a daily reporting email

        $workflows = $repo->getActiveWorkflows();
        $yesterday = Carbon::yesterday()->toDateString();
        $data = [];

        foreach ($workflows as $workflow) {
            $exportLists = $dataExportRepo->getForEspAccountId($workflow->esp_account_id);
            $data[] = ['workflow' => $workflow->name, 'lists' => []];

            foreach ($exportLists as $list) {
                $data = $logRepo->getDataForDate($list->target_list, $yesterday);
                $mtdCount = $logRepo->monthToDateCount($list->target_list, $yesterday);
                $received = $recordProcRepo->getFeedDateCount($list->feed_id, $yesterday);

                $data[$workflow->name]['lists'][] = [
                    'listName' => $list->target_list,
                    'usersReceived' => $received,
                    'usersSent' => $data->total_sent,
                    'duplicates' => $data->duplicates,
                    '3xDuplicates' => $data->egregious_duplicates,
                    'mtdCount' => $mtdCount
                ];
            }
        }

        Mail::send('emails.firstPartyEmail', $data, function ($message) {
            $message->to(self::TECH_EMAIL);
            $message->subject("Daily Export Report");
        });
    }
}