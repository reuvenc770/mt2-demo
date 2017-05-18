<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maknz\Slack\Facades\Slack;
use \Exception;

/**
 * Class RunTimeMonitorJob
 * @package App\Jobs
 * Two modes of operation: "monitor" mode analyzes the job_entries table for monitored jobs
 * fired during the specified datetime range that are approaching or have exceeded their
 * specified runtime threshold and updates their respective statuses accordingly. "resolved"
 * mode will set failed job statuses to RESOLVED in order to remove them from the failed job
 * reporting.
 */
class RunTimeMonitorJob extends MonitoredJob implements ShouldQueue
{

    CONST JOB_NAME = "RunTimeMonitor";
    CONST ROOM = '#mt2-dev-failed-jobs';
    protected $room;
    protected $date_range;
    protected $report;
    protected $mode;

    /**
     * @param null $mode, "monitor" or "resolve"
     * @param null $date1, integer indicating days back or start datetime, format YYYYMMDDhhmmss
     * @param null $date2, end datetime, format YYYYMMDDhhmmss
     */
    public function __construct($mode,$runtime_threshold,$date1,$date2=null)
    {

        parent::__construct(self::JOB_NAME.'_'.Carbon::now(),$runtime_threshold);

        $this->room = env('SLACK_CHANNEL',self::ROOM);

        //valid modes are 'monitor' and 'resolve'
        $this->mode = $mode;
        if(!preg_match("/^(monitor|resolve)$/",$this->mode)){
            JobTracking::addDiagnostic(array('errors'=>array('invalid mode '.$mode)),$this->tracking);
            JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
            throw new Exception("invalid mode: $mode");
        }
        JobTracking::addDiagnostic(array('notices'=>array('mode = '.$mode)),$this->tracking);


        //report structure
        $this->report = array(
            'summary' => array(),
            'notices' => array(),
            'warnings' => array(),
            'errors' => array()
        );

        //build datetime snippet
        if(preg_match("/^[0-9]{14}$/",$date1) && preg_match("/^[0-9]{14}$/",$date2)){
            $this->date_range = "BETWEEN $date1 AND $date2";
        }elseif(preg_match("/^[0-9]{1,3}$/",$date1)){
                $this->date_range = "> NOW() - INTERVAL $date1 DAY";
        }else{
            JobTracking::addDiagnostic(array('errors'=>array("invalid date_range: $date1 $date2")),$this->tracking);
            JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
            throw new Exception("invalid date_range: $date1 $date2");
        }
        JobTracking::addDiagnostic(array('notices'=>array('date_range = '.$this->date_range)),$this->tracking);

    }

    /**
     * @return int|void
     */
    public function handleJob(){

        try{
            //throw new Exception('runtime monitor failure test');
            if($this->mode=='monitor'){
                $this->updateStatuses();
                $this->generateReport();
            }else{
                $this->resolveJobs();
            }
            $this->sendReport();
        }catch (Exception $e){
            $this->sendRunTimeMonitorFailureAlert($e->getMessage());
            JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
            $this->failed();
        }
        return 1;
    }

    /**
     * updates jobs approaching or exceed runtime_seconds_threshold
     */
    private function updateStatuses(){
        $affected = JobTracking::updateJobStatuses($this->date_range);
        JobTracking::addDiagnostic(array('notices' => $affected . ' jobs statuses were updated'),$this->tracking);
    }

    /**
     * generates both a summary snapshot of job statuses for the specified daterange
     * and a detailed list of failed jobs under report[errors], and jobs approaching
     * runtime threshold under report[warnings].
     */
    private function generateReport(){
        $snapshot = JobTracking::generateRunTimeReport($this->date_range);
        $this->report['summary'][] = 'Snapshot of Job Queue as of '.Carbon::now()->toDateTimeString();
        $this->report['summary'][] = 'Date Range Analyzed: '.$this->date_range;
        $this->report['summary'][] = "Status\tCount";
        $this->report['summary'][] = "------\t-----";

        foreach($snapshot AS $row){
            $row->{'status '} = preg_replace("/^[0-9]{1,2} /","",$row->{'status '});
            $this->report['summary'][] = $row->{'status '}."\t".$row->count;
        }

        JobTracking::addDiagnostic(array('summary' => $this->report['summary']),$this->tracking);

        $badjobs = JobTracking::retrieveBadJobs($this->date_range);
        foreach($badjobs AS $job){
            $this->report[$job->type.'s'][] = (array) $job;
        }
    }

    /**
     * sets status to RESOLVED for the specified date range of jobs with status FAILED and FAILED_ACCEPTANCE_TEST
     */
    private function resolveJobs(){

        $affected = JobTracking::resolveJobs($this->date_range);

        $this->report['summary'][] = 'Specified Jobs RESOLVED, executed at '.Carbon::now();
        $this->report['summary'][] = 'Date Range RESOLVED: '.$this->date_range;
        $this->report['summary'][] = "Number of Jobs RESOLVED: $affected";

        JobTracking::addDiagnostic(array('notices' => $affected . ' jobs statuses were updated to RESOLVED'),$this->tracking);
    }

    /**
     * sends runtime monitoring report configured slack channel
     */
    private function sendReport(){

        $pretty = array("RunTimeMonitor Report");
        $pretty[] = 'SUMMARY';
        $pretty[] = '==========';
        $pretty[] = implode("\n",$this->report['summary']);
        $pretty[] = "\n\n";
        $pretty[] = "ERRORS";
        $pretty[] = "==========";
        $flat_errors = array();
        foreach($this->report['errors'] as $row){
            $flat_errors[] = json_encode($row,JSON_PRETTY_PRINT);
        }
        $pretty[] = implode("\n",$flat_errors);
        $pretty[] = "\n\n";
        $pretty[] = "WARNINGS";
        $pretty[] = "==========";
        $flat_warnings = array();
        foreach($this->report['warnings'] as $row){
            $flat_warnings[] = json_encode($row,JSON_PRETTY_PRINT);
        }
        $pretty[] = implode("\n",$flat_warnings);


        $pretty_report = implode("\n",$pretty);
        //print $pretty_report;

        Slack::to($this->room)->attach(['text' => $pretty_report])->send('Runtime Monitoring Report');
    }

    /**
     * reports execution failure to configured slack channel
     */
    private function sendRunTimeMonitorFailureAlert($error_message){
        Slack::to(self::ROOM)->send('ERROR ALERT: RunTimeMonitor failed to properly execute! '.$error_message);
    }
}
