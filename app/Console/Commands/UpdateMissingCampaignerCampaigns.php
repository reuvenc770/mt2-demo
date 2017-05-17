<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Facades\EspApiAccount;
use App\Jobs\UpdateMissingCampaignerCampaignsJob;

class UpdateMissingCampaignerCampaigns extends Command
{
    use DispatchesJobs;

    const ESP_NAME = 'Campaigner';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:UpdateMissingCampaignerCampaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $accounts = EspApiAccount::getAllAccountsByESPName( self::ESP_NAME );

        foreach ( $accounts as $current ) {
            if ( $current->enable_stats ) {
                $job = new UpdateMissingCampaignerCampaignsJob( $current->id , str_random( 16 ) );
                $this->dispatch( $job );
            } else {
                $this->info( 'Campaigner Account ' . $current->id . ' stats disabled. Aborting missing campaign job.' );
            }
        } 
    }
}
