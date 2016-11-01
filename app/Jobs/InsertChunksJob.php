<?php

namespace App\Jobs;

use Log;
use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use DB;
use App\Facades\JobTracking;

use App\Models\Email;
use App\Models\JobEntry;
use App\Jobs\Traits\PreventJobOverlapping;

class InsertChunksJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    protected $tracking;
    protected $size;
    protected $from;
    protected $to;
    const JOB_NAME = 'InsertInChunks';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($from, $to, $size, $tracking ) {
        $this->from = $from;
        $this->to = $to;
        $this->size = $size;
        $this->tracking = $tracking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle() {
        if ($this->jobCanRun(self::JOB_NAME)) {
            $this->createLock(self::JOB_NAME);
            $this->initJobEntry();

            Log::info( "Beginning job from {$this->from} to {$this->to}" );

            // get schemas

            // get max id
            $maxId = $this->getMaxId($this->from);
            $currentId = 0;

            while ($currentId < $maxId) {
                $segmentEnd = $this->getSegmentEnd($this->from, $currentId, $this->size);
                $segmentEnd = $segmentEnd !== null ? $segmentEnd : $maxId;
                echo "Would start at $currentId and end at $segmentEnd" . PHP_EOL;

                DB::statement("INSERT INTO {$this->to}
                    (email_id, deploy_id, esp_account_id, esp_internal_id, action_id, datetime, created_at, updated_at)

                    SELECT
                        email_id, deploy_id, esp_account_id, esp_internal_id, action_id, datetime, created_at, updated_at
                    FROM
                        {$this->from}
                    WHERE
                        {$this->from}.id BETWEEN $currentId AND $segmentEnd");

                $currentId = ++$segmentEnd;
            }

            $this->changeJobEntry( JobEntry::SUCCESS );
            $this->unlock(self::JOB_NAME);
        }

        else {
            echo "Still running {$this->jobName} - job level" . PHP_EOL;
        }

    }

    protected function initJobEntry () {
        JobTracking::startAggregationJob( self::JOB_NAME, $this->tracking );
    }

    protected function changeJobEntry ( $status ) {
        JobTracking::changeJobState( $status , $this->tracking);
    }

    public function failed() {
        $this->changeJobEntry( JobEntry::FAILED );
    }

    protected function getMaxId($table) {
        return DB::select("SELECT MAX(id) AS id FROM $table")[0]->id;
    }

    protected function getSegmentEnd($table, $start, $offset) {
        echo 'getting segment end: ' . $start . ' ' . $offset . PHP_EOL;
        $result =  DB::select("SELECT id FROM $table WHERE id > $start ORDER BY id LIMIT 1 OFFSET $offset");
        if (isset($result[0])) {
            return $result[0]->id;
        }
        return null;        
    }
}
