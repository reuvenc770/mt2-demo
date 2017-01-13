<?php

namespace App\Console\Commands;


use App\Models\AWeberReport;
use App\Repositories\ReportRepo;
use App\Services\API\AWeberApi;
use App\Services\EmailRecordService;
use App\Repositories\EmailRecordRepo;
use App\Models\Email;
use App\Models\RecordData;
use App\Services\AWeberReportService;use DaveJamesMiller\Breadcrumbs\Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Inspire extends Command
{
    use DispatchesJobs;
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
        $service = new AWeberReportService(new ReportRepo(new AWeberReport()), new AWeberApi(31), new EmailRecordService(new EmailRecordRepo(new Email(), new RecordData())));
        $service->blah();
    }
}
