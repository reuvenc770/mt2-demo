<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Traits\PreventOverlapping;
use App\Jobs\DataProcessingJob;

class PopulateAttributionRecordReport extends Command
{
    use DispatchesJobs, PreventOverlapping;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:populateAttrBaseRecords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populates the AttributionRecordReport table.';

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
        $this->dispatch( new DataProcessingJob( 'PopulateAttributionRecordReport' , str_random( 16 ) ) );
    }
}
