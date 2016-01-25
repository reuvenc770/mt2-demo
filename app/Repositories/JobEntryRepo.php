<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/25/16
 * Time: 3:12 PM
 */

namespace App\Repositories;


use App\Models\JobEntry;

class JobEntryRepo
{
    /**
     * @var JobEntry
     */
    protected $entry;

    /**
     * JobEntryRepo constructor.
     * @param JobEntry $entry
     */
    public function __construct(JobEntry $entry)
    {
        $this->entry = $entry;
    }

    /**
     * @param $jobName
     * @param $espName
     * @param $accountName
     * @return JobEntry
     */
    public function startEspJobReturnObject($jobName, $espName, $accountName){
        return $this->entry->firstOrNew(['job_name' => $jobName, 'account_name'=> $espName, 'account_number' => $accountName]);
    }

}