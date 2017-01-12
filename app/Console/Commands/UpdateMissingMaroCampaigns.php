<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Facades\EspApiAccount;
use Carbon\Carbon;
use App\Jobs\UpdateMissingMaroCampaignsJob;

class UpdateMissingMaroCampaigns extends Command
{
    use DispatchesJobs;

    const ESP_NAME = 'Maro';

    protected $lookBack;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:updateMissingMaroCampaigns';

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
        \Log::info( 'UpdateMissingMaroCampaigns initiated..' );

        $accounts = EspApiAccount::getAllAccountsByESPName( self::ESP_NAME );

        foreach ( $accounts as $current ) {
            \Log::info( "Generating job for Maro account ID {$current->id}" );

            $job = new UpdateMissingMaroCampaignsJob( $current->id , str_random( 16 ) );
            $this->dispatch( $job );
        }
    }
}
