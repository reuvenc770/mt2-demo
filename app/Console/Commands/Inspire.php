<?php

namespace App\Console\Commands;


use App\Services\API\BlueHornetApi;
use App\Services\BlueHornetSubscriberService;
use App\Services\CfsStatsService;
use App\Services\EmailRecordService;
use Illuminate\Console\Command;
use Illuminate\Foundation\Inspiring;
use Storage;
use App;
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
            $test  = App::make('App\\Services\\CfsStatsService');
        dd($test->getCreativeByOfferId(7491));
    }
}
