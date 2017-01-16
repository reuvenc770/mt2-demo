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
    protected $setFields;
    protected $expireFields;

    public function __construct(AttributionScheduleRepo $attributionScheduleRepo, $filterName)
    {
        $this->scheduleRepo = $attributionScheduleRepo;
        $this->setFields = config('scheduledfilters' . $filterName . '.set');
        $this->expireFields = config('scheduledfilters' . $filterName . '.expire');
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
        $preppedData = array();
        foreach($emails as $email){
            $date = isset($email['datetime']) ?
                Carbon::parse($email['datetime'])->addDays($days)->toDateString() : Carbon::today()->addDays($days)->toDateString();

            if(Carbon::parse($email['datetime'])->toDateString() > Carbon::today()->toDateString() ){
                $date = Carbon::today()->addDays($days)->toDateString();
            }
            
            $emailId = isset($email['email_id']) ? $email['email_id'] : $email;
            $preppedData[] = "(".join(",",[$emailId,"'".$date."'","NOW()","NOW()"]).")";

            if(count($preppedData) == 5000) {
                $this->scheduleRepo->insertScheduleBulk($preppedData);
                $preppedData = [];
            }

        }
        
        if(count($preppedData) > 0){
            $this->scheduleRepo->insertScheduleBulk($preppedData);
        }

    }

    public function getSetFields() {
        return array_keys($this->setFields);
    }

    public function returnFieldsForExpiration(){
        return $this->expireFields;
    }

    public function getSetFieldValue($field) {
        return $this->setFields[$field];
    }

    public function deleteSchedules($emails){
        return $this->scheduleRepo->bulkDelete($emails);
    }
}