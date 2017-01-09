<?php

namespace App\Console\Commands;

use App\Jobs\ProcessAweberUniques;
use App\Models\AWeberReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\SerializesModels;

class FindMissingStatsForAWeber extends Command
{
    use DispatchesJobs;
    use SerializesModels;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processAweberStats';
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
    public function handle()
    {
        $date = Carbon::today()->subDay(15)->toDateString();
        $rows = $this->report->where("datetime", '>=', $date)->get();
        foreach($rows as $row){
            $job = new ProcessAweberUniques($row->id,$row->esp_account_id,$row->info_url,AWeberReport::UNIQUE_OPENS, str_random(16));
            $this->dispatch($job);
            $job = new ProcessAweberUniques($row->id,$row->esp_account_id,$row->info_url,AWeberReport::UNIQUE_CLICKS, str_random(16));
            $this->dispatch($job);
        }

    }
}
