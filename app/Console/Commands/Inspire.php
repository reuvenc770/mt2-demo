<?php

namespace App\Console\Commands;

use App\Models\BlueHornetReport;
use App\Models\Email;
use App\Repositories\EmailRecordRepo;
use App\Repositories\ReportRepo;
use App\Services\API\BlueHornetApi;
use App\Services\BlueHornetReportService;
use App\Services\EmailRecordService;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Storage;

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
            echo "please work I am awesome";
    }
}
