<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Facades\JobTracking;
use Carbon\Carbon;
use Cron\CronExpression;

class ScheduledNotificationQueueJob extends MonitoredJob implements ShouldQueue
{
    const BASE_JOB_NAME = 'ScheduledNotificationQueueJob';

    protected $jobName;
    protected $contentType;
    protected $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( $contentType , $tracking , $runtimeThreshold )
    {
        $this->contentType = $contentType;
        $this->jobName = self::BASE_JOB_NAME . ":" . $this->contentType;
        $this->tracking = $tracking;

        parent::__construct( $this->jobName , $runtimeThreshold , $tracking );

        JobTracking::startAggregationJob( $this->jobName , $this->tracking );
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handleJob()
    {
        $service = \App::make( \App\Services\NotificationScheduleService::class );

        $schedules = $service->getAllActiveNotifications();

        foreach ( $schedules as $current ) {
            $cron = CronExpression::factory( $current->cron_expression );
            $nextRunDate = Carbon::parse( $cron->getNextRunDate()->format('Y-m-d H:i:s') );

            if ( $nextRunDate->isToday() ) {
                \Log::info( $current->title . " scheduled for today." );

                $nextRunInSeconds = Carbon::now()->diffInSeconds( Carbon::parse( $cron->getNextRunDate()->format('Y-m-d H:i:s') ) );            

                \Log::info( $nextRunInSeconds );
            } else {
                \Log::info( $current->title . ' scheduled for ' . $cron->getNextRunDate()->format('Y-m-d H:i:s') );
            }
        }
    }
}
