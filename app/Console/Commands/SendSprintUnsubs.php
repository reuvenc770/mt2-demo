<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendSprintUnsubs;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SendSprintUnsubs extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:sendSprintUnsubs {--lookBack=1} {--queue=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs campaigns from CSV, finds unsubs, and sends a file via FTP to Sprint.';

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
        $this->table( [ 'Option' , 'Value' ] , [
            [ 'option' => 'lookback' , 'value' => $this->option( 'lookBack' ) ] ,
            [ 'option' => 'queue' , 'value' => $this->option( 'queue' ) ] ,
        ] );

        $job = new SendSprintUnsubs( $this->option( 'lookBack' ) );

        $job->onQueue( $this->option( 'queue' ) );

        $this->dispatch( $job );
    }
}
