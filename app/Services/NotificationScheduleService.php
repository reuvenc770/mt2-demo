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

    public function getAllActiveNotifications () {
        return $this->repo->getAllActiveNotifications();
    }
}
