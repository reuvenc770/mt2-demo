<?php

namespace App\Jobs;

use Log;
use App\Jobs\Job;
use App\Models\JobEntry;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;
use DB;
use App\Facades\JobTracking;
use App\Jobs\Traits\PreventJobOverlapping;
use App\Models\EmailIdHistory;

class InflateEmailHistoriesJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, PreventJobOverlapping;

    const JOB_NAME = 'InflateEmail';
    private $tracking;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tracking ) {
        $this->tracking = $tracking;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(EmailIdHistory $model) {
        if ($this->jobCanRun(self::JOB_NAME)) {
            $this->createLock(self::JOB_NAME);
            $this->initJobEntry();

            $model->each(function($row, $id) {
                $oldEmailIds = json_decode($row->old_email_id_list, true);
                $newEmailId = $row->email_id;

                foreach($oldEmailIds as $oldId) {
                    DB::insert('INSERT INTO inflated_email_histories (final_email_id, old_email_id) values (?, ?)', [$newEmailId, $oldId]);
                }

            });

            $this->changeJobEntry( JobEntry::SUCCESS );
            $this->unlock(self::JOB_NAME);
        }

        else {
            echo "Still running {self::JOB_NAME} - job level" . PHP_EOL;
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

}