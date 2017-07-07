<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Repositories;

use App\Models\NotificationSchedule;
use App\Models\NotificationLog;

use App\Repositories\Traits\ToggleBooleanColumn;

use Carbon\Carbon;
use Cron\CronExpression;
use Cache;

class NotificationScheduleRepo {
    use ToggleBooleanColumn;

    protected $schedules;
    protected $logs;

    public function __construct ( NotificationSchedule $schedules , NotificationLog $logs ) {
        $this->schedules = $schedules;
        $this->logs = $logs;
    }

    public function getAllActiveNotifications ( $contentType ) {
        $result = $this->schedules->where( 'status' , 1 );

        if ( $contentType != 'all' ) {
            $result->where( 'content_key' , $contentType );
        }

        $scheduleCollection = collect( [] );

        if ( $result->count() ) {
            $scheduleCollection = $result->get();

            $locks = [];

            foreach ( $scheduleCollection as $index => $current ) {
                $cron = CronExpression::factory( $current->cron_expression );
                $current->nextRunDatetime = Carbon::parse( $cron->getNextRunDate()->format('Y-m-d H:i:s') );

                $current->isToday = ( Carbon::parse( $current->nextRunDatetime ) )->isToday();
                $current->nextRunInSeconds = Carbon::now()->diffInSeconds(
                    Carbon::parse( $current->nextRunDatetime )
                );

                if ( $current->level == 'critical' ) {
                    $current->isCritical = true;
                }

                $current->hasLogs = $this->hasLogs( $current->content_key , $current->content_lookback );

                $locks []= [
                    "index" => $index ,
                    "content_key" => $current->content_key ,
                    "nextRunDatetime" => $current->nextRunDatetime ,
                    "isCritical" => $current->isCritical
                ];
            }

            foreach ( $locks as $current ) {
                if ( Cache::has( $current[ 'content_key' ] ) ) {
                    $scheduleCollection->forget( $current[ 'index' ] );
                } else {
                    if ( $current[ 'isCritical' ] ) { #throttling critical notifications to send every 10 minutes.
                        $expires = Carbon::now()->addMinutes( 10 );
                    } else { #prevents queuing duplicate notifications
                        $expires = Carbon::parse( $current[ 'nextRunDatetime' ] )->addSeconds( 30 );
                    }

                    Cache::put( $current[ 'content_key' ] , 1 , $expires );
                }
            }
        }

        return $scheduleCollection;
    }

    public function log ( $contentType , $content ) {
        return $this->logs->create( [
            'content_key' => $contentType ,
            'content' => $content
        ] );
    }

    public function hasLogs ( $contentType , $lookback ) {
        $result = $this->logs
            ->where( 'content_key' , $contentType )
            ->whereBetween( 'created_at' , [  
                Carbon::now()->subHours( $lookback )->toDateTimeString() ,
                Carbon::now()->toDateTimeString()
            ] );

        return $result->count() > 0;
    }

    public function getLogs ( $contentType , $lookback ) {
        $result = $this->logs
            ->where( 'content_key' , $contentType )
            ->whereBetween( 'created_at' , [  
                Carbon::now()->subHours( $lookback )->toDateTimeString() ,
                Carbon::now()->toDateTimeString()
            ] );

        $logs = [];
        if ( $result->count() > 0 ) {
            $records = $result->get();

            foreach ( $records as $current ) {
                $logs []= json_decode( $current->content , true );
            }
        }

        return $logs;
    }

    public function getModel () {
        return $this->schedules;
    }

    public function getUnscheduledLogs () {
        $result = $this->logs
            ->select( 'notification_logs.content_key' , 'notification_logs.content' )
            ->leftJoin( 'notification_schedules as ns' , 'notification_logs.content_key' , '=' , 'ns.content_key' )
            ->where( 'ns.id' , null )
            ->groupBy( 'notification_logs.content_key' );

        if ( $result->count() <= 0 ) {
            return [];
        }

        return $result->get()->toArray();
    }

    public function getDistinctContentKeys () {
        return collect( \DB::select( 'select distinct( `content_key` ) from notification_logs' ) )->pluck( 'content_key' )->toArray(); 
    }

    public function updateOrCreate ( $fields ) {
        $id = null;

        if ( isset( $fields[ 'id' ] ) && is_numeric( $fields[ 'id' ] ) ) {
            $id = $fields[ 'id' ];
        }

        return $this->schedules->updateOrCreate( [ 'id' => $id ] , $fields );
    }

    public function toggleStatus ( $id , $currentStatus ) {
        return $this->toggleBooleanColumn(
            $this->schedules ,
            $id , 
            'status' ,
            $currentStatus
        );        
    }
}
