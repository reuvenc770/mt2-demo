<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/5/16
 * Time: 4:40 PM
 */

namespace App\Services;


use App\Repositories\AttributionScheduleRepo;
use Log;
class ScheduledFilterService
{
    private $scheduleRepo;

    public function __construct(AttributionScheduleRepo $attributionScheduleRepo)
    {
        $this->scheduleRepo = $attributionScheduleRepo;
    }

    public function getRecordsByDate($date){
       try{
          return $this->scheduleRepo->getRecordsByDate($date);
       } catch (\Exception $e){
           $class = get_class($this->scheduleRepo);
           Log::error("Scheduled Filter Service failed to retrieve records for {$class}: {$e->getMessage()} ");
       }
    }
}