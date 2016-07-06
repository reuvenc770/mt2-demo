<?php

namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Event;

class CommitAttributionJob extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AttributionRecordTruthRepo $sourceRepo, AttributionStrategy $strategy) {
        $records = $sourceRepo->getTransientRecords();

        foreach ($records as $record) {
            $beginDate = Carbon::today()->subDay(10)->format('Y-m-d'); // don't hard-code this

            $potentialReplacements = $record->email()
                                            ->emailClientInstances()
                                            ->where('capture_date', '>', $beginDate)
                                            ->get();

            foreach ($potentialReplacements as $x) {
                if ($client = $strategy->changesAttribution($x)) {
                    $record->changeAttribution($client);
                }
            }
        }

        Event::fire('');
    }
}
