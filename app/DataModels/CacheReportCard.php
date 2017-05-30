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
    protected $numberOfEntries = 0;
    /**
     * @var array
     */
    protected $entries = [];

    CONST DEFAULT_MAIL = "alphateam@zetainteractive.com";
    CONST EMERGENCY_MAIL = "espken@zetaglobal.com";
    const TECH_EMAIL = "tech.team.mt2@zetaglobal.com";

    /**
     * @param $name
     * @return CacheReportCard
     */
    static function makeNewReportCard($name){
        $obj = new self;
        $obj->name = $name.str_random(10);
        Cache::put($name, $obj, Carbon::now()->addMinutes(60));
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

    /**
     * @return IO void
     */
    public function mail() {
        $mailObject = [
            "date" => Carbon::today()->toDateString(),
            "owner" => $this->owner,
            "warning" => false,
            "entries" =>[]
        ];

        foreach ($this->entries as $entry) {
            if(!$mailObject['warning']){
                $mailObject['warning'] = ($entry->getOriginalTotal() - $entry->getFinalTotal()) <= 10;
            }
            $mailObject["entries"][] = [
                "fileName" => $entry->getFileName(),
                "originalTotal" => $entry->getOriginalTotal(),
                "finalTotal" => $entry->getFinalTotal(),
                "globallySuppressed" => $entry->getGloballySuppressed(),
                "listOfferSuppressed" => $entry->getListOfferSuppressed(),
                "offersSuppressedAgainst" => $entry->getOffersSuppressedString(),
            ];
        }

        Mail::send('emails.deploySuppression', $mailObject, function ($message) use ($mailObject) {
            $toEmail = $mailObject['warning'] ? self::EMERGENCY_MAIL : self::DEFAULT_MAIL;
            $message->to($toEmail);
            $message->to(self::TECH_EMAIL);
            $subject = $mailObject['warning'] ? "ALERT " : "";
            $subject = "{$subject} Deploy Suppression Report for {$mailObject['owner']}";
            $message->subject($subject);
        });
    }
}