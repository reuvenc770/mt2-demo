<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Facades\JobTracking;
use Carbon\Carbon;
use Cron\CronExpression;

class ScheduledNotificationQueueJob extends MonitoredJob
{
    use DispatchesJobs;

    const NOTIFICATION_QUEUE = 'scheduled_notifications';

    protected $baseJobName = 'ScheduledNotificationQueueJob';
    protected $jobName;
    protected $contentType;
    protected $tracking;
    protected $runtimeThreshold;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $contentType , $tracking , $runtimeThreshold )
    {
        $this->runtimeThreshold = $runtimeThreshold;
        $this->contentType = $contentType;
        $this->jobName = $this->baseJobName . ":" . $this->contentType;
        $this->tracking = $tracking;

        parent::__construct(
            $this->jobName ,
            $runtimeThreshold,
            $tracking
        );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $service = \App::make( \App\Services\NotificationScheduleService::class );

        foreach ( $service->getAllActiveNotifications( $this->contentType ) as $notification ) {
            if ( !$notification->isToday || !$notification->hasLogs ) {
                continue;
            }

            $jobDelay = 0;
            if ( !$notification->isCritical ) {
                $jobDelay = $notification->nextRunInSeconds;
            }

            $worker = \App::make( \App\Jobs\ScheduledNotificationWorkerJob::class , [
                $notification ,
                str_random( 16 ) ,
                $this->runtimeThreshold,
                $notification->isCritical
            ] );

            $worker->delay( $jobDelay )->onQueue( self::NOTIFICATION_QUEUE );

            $this->dispatch( $worker );
        }
    }
}
