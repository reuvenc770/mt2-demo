<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAweberUniques;
use App\Models\AWeberReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\EspApiAccountService;

class FindMissingStatsForAWeber extends Command
{
    use DispatchesJobs;
    use InteractsWithQueue;
    use SerializesModels;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aweber:processUniques {lookback?}';
    protected $report;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grab Unique Stats for Aweber';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(AWeberReport $report)
    {
        parent::__construct();
        $this->report = $report;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle( EspApiAccountService $espServ )
    {
        $lookBack = $this->argument('lookback') ?: 15;
        $date = Carbon::today()->subDay($lookBack)->toDateString();
        $rows = $this->report->where("datetime", '>=', $date)->get();
        foreach($rows as $row){
            if ( $espServ->statsEnabledForAccount( $row->esp_account_id ) ) {
                $job = (new ProcessAweberUniques($row->id,$row->esp_account_id,$row->info_url,AWeberReport::UNIQUE_OPENS, str_random(16)))->onQueue("AWeber");
                $this->dispatch($job);
                $job = (new ProcessAweberUniques($row->id,$row->esp_account_id,$row->info_url,AWeberReport::UNIQUE_CLICKS, str_random(16)))->onQueue("AWeber");
                $this->dispatch($job);
            } else {
                $this->info( 'Stats disabled for account ID ' . $row->esp_account_id );
            }
        }

    }
}
