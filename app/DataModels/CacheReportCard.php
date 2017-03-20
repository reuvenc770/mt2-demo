<?php
/**
 * Created by PhpStorm.
 * User: codedestroyer
 * Date: 2/23/17
 * Time: 2:15 PM
 */

namespace App\DataModels;
use Cache;
use Carbon\Carbon;
class CacheReportCard
{

    /**
     * @var
     */
    public $name;
    /**
     * @var
     */
    protected $owner;
    /**
     * @var
     */
    protected $numberOfEntries;
    /**
     * @var array
     */
    protected $entries = [];


    /**
     * @param $name
     * @return CacheReportCard
     */
    static function makeNewReportCard($name){
        $obj = new self;
        $obj->name = $name.str_random(10);
        Cache::put($name, $obj, Carbon::now()->addMinutes(30));
        return $obj;
    }

    /**
     * @param $name
     * @return mixed
     */
    static function getReportCard($name){
        return Cache::get($name);
    }

    /**
     * @param $number
     */
    public function setNumberOfEntries($number){
        $this->numberOfEntries = $number;
        $this->updateCache();
    }

    /**
     * @param ReportEntry $report
     */
    public function addEntry(ReportEntry $report){
        $this->entries[] = $report;
        $this->updateCache();
    }

    /**
     * @return array
     */
    public function getEntries(){
        return $this->entries;
    }

    /**
     *
     */
    public function nextEntry(){
        $this->numberOfEntries--;
        $this->updateCache();
    }

    /**
     * @return mixed
     */
    public function getName(){
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isReportCardFinished(){
        if($this->numberOfEntries == 0){
            Cache::forget($this->name);
            return true;
        } else {
            return false;
        }
    }

    /**
     *
     */
    private function updateCache(){
        Cache::put($this->name, $this, Carbon::now()->addMinutes(30));
    }

    /**
     * @param $owner
     */
    public function setOwner($owner){
        $this->owner = $owner;
    }

    /**
     * @return mixed
     */
    public function getOwner(){
        return $this->owner;
    }
}