<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Carbon\Carbon;

class BestMoneySearchGetResponseContactUploadCommand extends Command
{
    use DispatchesJobs;

    const RUNTIME_THRESHOLD = 180;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'EspContactUpload:BestMoneySearch {--D|daysBack=1 : How far back to look for new records.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Queues job that uploads new records from RexDirectBMS to GetResponse ESP';

    protected $dateRange = [];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->processOptions();

        $tracking = str_random( 16 );

        $this->dispatch( \App::make( \App\Jobs\BestMoneySearchGetResponseContactUploadJob::class , [ $this->dateRange , $tracking , self::RUNTIME_THRESHOLD ] ) );
    }

    protected function processOptions () {
        $this->dateRange = [
            'start' => Carbon::now()->subDays( $this->option( 'daysBack' ) )->toDateTimeString() ,
            'end' => Carbon::now()->toDateTimeString()
        ];
    }
}
