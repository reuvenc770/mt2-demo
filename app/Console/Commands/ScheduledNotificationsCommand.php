<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Jobs\ScheduledNotificationQueueJob;

class ScheduledNotificationsCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:scheduled { --K|contentKey=all : Specific logs to notify for. Default is all. } { --runtime-threshold=default : Threshold for monitoring. }';

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
        $job = \App::make( ScheduledNotificationQueueJob::class , [
            $this->option( 'contentKey' ) ,
            str_random( 16 ) ,
            $this->option( 'runtime-threshold' )
        ] );
        
        $job->onQueue( ScheduledNotificationQueueJob::NOTIFICATION_QUEUE );

        $this->dispatch( $job );
    }
}
