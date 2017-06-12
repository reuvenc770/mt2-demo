<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Repositories\NotificationScheduleRepo;

class NotificationScheduleService {
    protected $repo;

    public function __construct ( NotificationScheduleRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAllActiveNotifications ( $contentType ) {
        return $this->repo->getAllActiveNotifications( $contentType );
    }

    public function log ( $contentType , $content ) {
        return $this->repo->log( $contentType , $content );
    }

    public function hasLogs ( $contentType , $lookback ) {
        return $this->repo->hasLogs( $contentType , $lookback );
    }
}
