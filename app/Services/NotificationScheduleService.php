<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Services;

use App\Repositories\NotificationScheduleRepo;
use App\Services\ServiceTraits\PaginateList;

class NotificationScheduleService {
    use PaginateList;

    protected $repo;

    public function __construct ( NotificationScheduleRepo $repo ) {
        $this->repo = $repo;
    }

    public function getModel () {
        return $this->repo->getModel();
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

    public function getLogs ( $contentType , $lookback ) {
        return $this->repo->getLogs( $contentType , $lookback );
    }

    public function getUnscheduledLogs () {
        return $this->repo->getUnscheduledLogs();
    }
}
