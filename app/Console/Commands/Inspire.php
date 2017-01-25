<?php

namespace App\Console\Commands;


use App\Jobs\ProcessThirdPartyMaroRecords;
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
use App\Models\ActionType;
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
        echo "The woods are lovely, dark and deep. But I have promises to keep, and miles to go before I sleep.";
    }
}
