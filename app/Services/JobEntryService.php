<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/25/16
 * Time: 3:14 PM
 */

namespace App\Services;


use App\Repositories\JobEntryRepo;
use Carbon\Carbon;
use Maknz\Slack\Facades\Slack;
class JobEntryService
{
    protected $repo;
    CONST ROOM = "#mt2-dev-failed-jobs";

    public function __construct( JobEntryRepo $repo)
    {
        $this->repo = $repo;
    }

    public function startEspJob($jobName, $espName, $accountName)
    {
        $espJob = $this->repo->startEspJobReturnObject($jobName, $espName, $accountName);
        $espJob->time_started = Carbon::now();
        $espJob->attempts = 1;
        $espJob->status = "Running";
        $espJob->save();

    }

    public function failEspJob($jobName, $espName, $accountName, $tries)
    {
        $job =  $this->repo->getJob($jobName, $espName, $accountName);
        $job->status = "Failed";
        $job->attempts = $tries;
        $job->time_finished = Carbon::now();
        $job->save();

        if((bool)env('SLACK_ON', false)) {
            Slack::to(self::ROOM)->send("Job {$jobName} has failed for {$espName}-{$accountName}");
        }


    }

    public function finishEspJob($jobName, $espName, $accountName){}

}