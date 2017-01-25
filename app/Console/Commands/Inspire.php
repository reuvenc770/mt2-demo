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
        $job = new ProcessThirdPartyMaroRecords("Dima","4",str_random(16),336,'6815ac2425f6a58b648955006b40e5c2c7e3503d');
        $this->dispatch($job);
    }
}
