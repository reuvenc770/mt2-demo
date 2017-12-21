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
use Mail;
use App\DataModels\CacheReportCard;

class NoUserCacheReportCard extends CacheReportCard
{
    static function makeNewReportCard($name){
        $obj = new self;
        $obj->name = $name.str_random(10);
        Cache::put($name, $obj, Carbon::now()->addMinutes(60));
        return $obj;
    }   

    public function mail() { echo "Suppressing mail notification for '{$this->name}'"; }
}