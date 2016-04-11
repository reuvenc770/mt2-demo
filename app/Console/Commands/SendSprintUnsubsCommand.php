<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendSprintUnsubs;
use Illuminate\Foundation\Bus\DispatchesJobs;

class SendSprintUnsubsCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp:sendSprintUnsubs {--ftpCleanup=0} {--lookBack=3} { --dayLimit=3 } {--queue=default}';

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
        if ( $this->option( 'ftpCleanup' ) == 1 ) {
            $this->info( 'Crawling FTP acocunt for old campaign files.' );
        } else {
            $this->table( [ 'Option' , 'Value' ] , [
                [ 'option' => 'lookBack' , 'value' => $this->option( 'lookBack' ) ] ,
                [ 'option' => 'dayLimit' , 'value' => $this->option( 'dayLimit' ) ] ,
                [ 'option' => 'queue' , 'value' => $this->option( 'queue' ) ] ,
            ] );
        }

        $job = new SendSprintUnsubs( $this->option( 'lookBack' ) , $this->option( 'dayLimit' ) , str_random( 16 ) , $this->option( 'ftpCleanup' ) );

        $job->onQueue( $this->option( 'queue' ) );

        $this->dispatch( $job );
    }
}
