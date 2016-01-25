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

class JobEntryService
{
    protected $repo;

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

    public function finishEspJob($jobName, $espName, $accountName){}

}