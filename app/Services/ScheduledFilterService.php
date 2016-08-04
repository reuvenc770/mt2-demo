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
    protected $fields;
    public $boolValue;
    public function __construct(AttributionScheduleRepo $attributionScheduleRepo, $filterName)
    {
        $this->scheduleRepo = $attributionScheduleRepo;
        $this->fields = config( 'scheduledfilters.' . $filterName . '.column' );
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
        $preppedData = array();
        foreach($emails as $email){
            $date = isset($email['datetime']) ?
                Carbon::parse($email['datetime'])->addDays($days)->toDateString() : Carbon::today()->addDays($days)->toDateString();

            $emailId = isset($email['email_id']) ? $email['email_id'] : $email;
            $preppedData[] = "(".join(",",[$emailId,"'".$date."'","NOW()","NOW()"]).")";

            if(count($preppedData) == 5000) {
                try {
                    $this->scheduleRepo->insertScheduleBulk($preppedData);
                } catch (\Exception $e) {
                    $class = get_class($this->scheduleRepo);
                    Log::error("Scheduled Filter Service failed to insert records Bulk for {$class}: {$e->getMessage()} ");
                }
                $preppedData = [];
            }
        }

    }

    public function getFields() {
        return array_keys($this->fields);
    }

    public function getDefaultFieldValue($field) {
        return $this->fields[$field];
    }
}