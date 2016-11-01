<?php

namespace App\Console\Commands;


use App\Library\Campaigner\Authentication;
use App\Library\Campaigner\ContactManagement;
use App\Library\Campaigner\ListAttributes;
use App\Library\Campaigner\ListAttributesFilter;
use App\Services\API\BlueHornetApi;
use App\Services\AWeberReportService;
use App\Services\BlueHornetSubscriberService;
use App\Services\EmailRecordService;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Storage;
use Maknz\Slack\Facades\Slack;
use App\Facades\SlackLevel;
class Inspire extends Command
{
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
        Slack::to('@pcunningham')->send("sdfsdf");
        SlackLevel::to('@pat')->send("sdfsdf");
    }
}
