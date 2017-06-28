<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class NotificationSchedule extends Facade {
    protected static function getFacadeAccessor() { return 'notificationSchedule'; }
}
