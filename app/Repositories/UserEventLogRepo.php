<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/22/16
 * Time: 10:11 AM
 */

namespace App\Repositories;


use App\Models\UserEventLog;
use Log;
class UserEventLogRepo
{
    protected $userEventLog;

    public function __construct(UserEventLog $eventLog)
    {
        $this->userEventLog = $eventLog;
    }

    public function insertEvent($event){
        try {
            $this->userEventLog->create($event);
        } catch(\Exception $e){
            Log::error("User Event Log Error:: ". $e->getMessage());
        }
    }
}