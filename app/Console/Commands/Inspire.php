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
        $bh = new BlueHornetReportService(new ReportRepo(new BlueHornetReport()), new BlueHornetApi("BlueHornet",10), new EmailRecordService(new EmailRecordRepo( new Email())));
        $file = $bh->getFile("export_files/echo7/35500/2016-03-25/07/36/20/message_contacts_data_35500_9330658_20160325_073620.xml");
        Storage::put('test.xml', $file);
    }
}
