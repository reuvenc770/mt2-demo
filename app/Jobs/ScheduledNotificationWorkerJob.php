<?php

namespace App\Jobs;

use App\Jobs\ScheduledNotificationQueueJob;
use Mail;
use Maknz\Slack\Facades\Slack;

class ScheduledNotificationWorkerJob extends ScheduledNotificationQueueJob
{
    protected $baseJobName = 'ScheduledNotificationWorkerJob';
    protected $schedule;

    public function __construct( $schedule , $tracking , $runtimeThreshold , $isCritical = false )
    {
        $this->schedule = $schedule;

        if ( $isCritical ) {
            $this->baseJobName .= ':CRITICAL';
        }

        parent::__construct( $this->schedule->content_key , $tracking , $runtimeThreshold );
    }

    public function handleJob()
    {
        $service = \App::make( \App\Services\NotificationScheduleService::class );
        $logs = $service->getLogs( $this->schedule->content_key , $this->schedule->content_lookback );

        if ( $this->schedule->use_email ) {
            $emailRecipients = $this->schedule->email_recipients;
            $subject = $this->schedule->title;

            Mail::send(
                $this->schedule->email_template_path  ,
                $logs , #do lookback for logs in service
                function ( $message ) use ( $emailRecipients , $subject ) {
                    $message->to( $emailRecipients );
                    $message->subject( $subject . ' ' . Carbon::now()->toCookieString() );
                    $message->priority( 1 );
                }
            );
        }

        if ( $this->schedule->use_slack ) {
            foreach ( $logs as $log ) {
                Slack::to( $this->schedule->slack_recipients )
                    ->send(
                        view( $this->schedule->slack_template_path , json_decode( $log->content ) )
                    );
            }
        }
    }
}
