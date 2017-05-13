<?php

namespace App\Jobs;

use Carbon\Carbon;
use App\Models\JobEntry;
use App\Facades\JobTracking;
use App\Jobs\MonitoredJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
use \Exception;

class RunTimeMonitorJob extends MonitoredJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    CONST JOB_NAME = "RunTimeMonitor";
    protected $runtime_seconds_threshold = 360;
    protected $date_range;
    protected $report;
    protected $mode;

    /**
     * Create a new runtime monitor instance.
     *
     * @return void
     */
    public function __construct($mode=null,$date1=null,$date2=null)
    {

        //valid modes are 'monitor' and 'resolve'
        $this->mode = $mode==null ? 'monitor' : $mode;
        #TODO, validate mode

        //report structure
        $this->report = array(
            'summary' => array(),
            'notices' => array(),
            'warnings' => array(),
            'errors' => array()
        );

        //build datetime snippet
        $date1 = $date1==null ? 3 : $date1;
        if(preg_match("/[0-9]{1,}$/",$date1)){
            $this->date_range = "NOW() - INTERVAL $date1 DAY";
        }elseif(false){
            #TODO, add support for start and end datetimes
        }else{
            #TODO, fail, invalid date range
        }

        parent::__construct(self::JOB_NAME.'_'.Carbon::now());

    }

    public function handleJob(){

        try{
            if($this->mode=='monitor'){
                $this->updateStatuses();
                $this->generateReport();
            }else{
                $this->resolveJobs();
            }
            $this->sendReport();
        }catch (Exception $e){
            $this->sendRunTimeMonitorFailureAlert();
            JobTracking::changeJobState(JobEntry::FAILED,$this->tracking);
            $this->failed();
        }
        return 1;
    }

    private function updateStatuses(){
        $affected = DB::update("UPDATE job_entries
                                SET status=
                                CASE
                                    WHEN time_started < NOW() - INTERVAL IFNULL(runtime_seconds_threshold,3600) SECOND THEN 10
                                    WHEN time_started < NOW() - INTERVAL TRUNCATE(IFNULL(runtime_seconds_threshold,3600)*0.75,0) SECOND THEN 9
                                ELSE status
                                END
                                WHERE status IN(1,7) AND time_fired > ".$this->date_range."
                                #AND runtime_seconds_threshold IS NOT NULL
                                ");
        JobTracking::addDiagnostic(array('notices' => $affected . ' jobs statuses were updated'),$this->tracking);

    }

    private function generateReport(){
        $snapshot = DB::select("SELECT
                                CASE
                                  WHEN status=5 THEN '1 QUEUED'
                                  WHEN status=4 THEN '2 WAITING'
                                  WHEN status=6 THEN '3 SKIPPED'
                                  WHEN status=1 THEN '4 RUNNING'
                                  WHEN status=7 THEN '5 RUNNING ACCEPTANCE TEST'
                                  WHEN status=2 THEN '6 SUCCESS'
                                  WHEN status=9 THEN '7 RUNTIME WARNING'
                                  WHEN status=10 THEN '8 RUNTIME ERROR'
                                  WHEN status=3 THEN '9 FAILED'
                                  WHEN status=8 THEN '10 ACCEPTANCE TEST FAILED'
                                  WHEN status=11 THEN '11 RESOLVED'
                                END as `status `,
                                COUNT(status) AS count
                                FROM job_entries
                                WHERE time_fired > ".$this->date_range."
                                GROUP BY `status `
                                ORDER BY `status `
                              ");

        $this->report['summary'] = array('Snapshot of Job Queue as of '.Carbon::now());
        $this->report['summary'] = array('Date Range Analyzed: '.$this->date_range);
        $this->report['summary'][] = "Status\tCount";

        foreach($snapshot AS $row){
            $row->{'status '} = preg_replace("/^\d /","",$row->{'status '});
            $this->report['summary'][] = $row->{'status '}."\t".$row->count;
        }


        JobTracking::addDiagnostic(array('notices' => $this->report['summary']),$this->tracking);

        $badjobs = DB::select("SELECT
                                CASE
                                WHEN status=9 THEN 'warning'
                                ELSE 'error'
                                END AS type,
                                CASE
                                WHEN status=9 THEN 'RUNTIME WARNING'
                                WHEN status=10 THEN 'RUNTIME ERROR'
                                WHEN status=3 THEN 'FAILED'
                                WHEN status=8 THEN 'ACCEPTANCE TEST FAILED'
                                END as message,
                                id,
                                job_name,
                                runtime_seconds_threshold,
                                time_fired,
                                time_started,
                                time_finished,
                                attempts,
                                status
                                FROM job_entries
                                WHERE time_fired > ".$this->date_range."
                                AND status IN(3,8,9,10)
                              ");

        foreach($badjobs AS $job){
            $this->report[$job->type.'s'][] = (array) $job;
        }

    }

    private function resolveJobs(){
        #TODO, update failed jobs to status RESOLVED for specified date range
    }

    private function sendReport(){

        $pretty = array('SUMMARY','==========');
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
        foreach($this->report['errors'] as $row){
            $flat_warnings[] = json_encode($row,JSON_PRETTY_PRINT);
        }
        $pretty[] = implode("\n",$flat_warnings);


        $pretty_report = implode("\n",$pretty);
        print $pretty_report;

        #TODO, where do we send, slack channel, email?
    }

    private function sendRunTimeMonitorFailureAlert(){
        #TODO, route to critical alert channel, highest escalation
    }
}
