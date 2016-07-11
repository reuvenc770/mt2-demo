<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 7/5/16
 * Time: 4:40 PM
 */

namespace App\Services;


use App\Repositories\AttributionScheduleRepo;
use Carbon\Carbon;
use Log;
class ScheduledFilterService
{
    private $scheduleRepo;
    public $fieldName;
    public $boolValue;
    public function __construct(AttributionScheduleRepo $attributionScheduleRepo, $filterName)
    {
        $this->scheduleRepo = $attributionScheduleRepo;
        $this->fieldName = config( 'scheduledfilters.' . $filterName . '.column' );
        $this->boolValue = config( 'scheduledfilters.' . $filterName . '.value' );
    }

    public function getRecordsByDate($date){
       try{
          return $this->scheduleRepo->getRecordsByDate($date);
       } catch (\Exception $e){
           $class = get_class($this->scheduleRepo);
           Log::error("Scheduled Filter Service failed to retrieve records for {$class}: {$e->getMessage()} ");
       }
    }

    public function insertScheduleFilter($emailId,$days){
        $date = Carbon::today()->addDays($days)->toDateString();
        try{
            return $this->scheduleRepo->insertSchedule($emailId,$date);
        } catch (\Exception $e){
            $class = get_class($this->scheduleRepo);
            Log::error("Scheduled Filter Service failed to insert records for {$class}: {$e->getMessage()} ");
        }
    }

    public function insertScheduleFilterBulk($emails,$days){
        $date = Carbon::today()->addDays($days)->toDateString();
        $preppedData = array();
        foreach($emails as $email){
            $preppedData[] = "(".join(",",[$email['email_id'],"'".$date."'","NOW()","NOW()"]).")";
        }
        try{
            $this->scheduleRepo->insertScheduleBulk($preppedData);
        } catch (\Exception $e){
            $class = get_class($this->scheduleRepo);
            Log::error("Scheduled Filter Service failed to insert records Bulk for {$class}: {$e->getMessage()} ");
        }
    }
}