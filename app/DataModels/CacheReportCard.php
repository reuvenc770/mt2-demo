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

    public $name;
    protected $numberOfEntries;
    protected $entries = [];


    static function makeNewReportCard($name){
        $obj = new self;
        $obj->name = $name.str_random(10);
        Cache::put($name, $obj, Carbon::now()->addMinutes(30));
        return $obj;
    }
    
    static function getReportCard($name){
        return Cache::get($name);
    }

    public function setNumberOfEntries($number){
        $this->numberOfEntries = $number;
        $this->updateCache();
    }

    public function addEntry(ReportEntry $report){
        $this->entries[] = $report;
        $this->updateCache();
    }
    public function nextEntry(){
        $this->numberOfEntries--;
        $this->updateCache();
    }

    public function getName(){
        return $this->name;
    }

    public function isReportCardFinished(){
        return $this->numberOfEntries == 0;
    }
    
    private function updateCache(){
        Cache::put($this->name, $this, Carbon::now()->addMinutes(30));
    }
}