<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Traits\PreventOverlapping;
use App\Jobs\ProcessFirstPartyMissedFeedRecordsJob;
use App\Jobs\ProcessFirstPartyFeedRecordsJob;
use App\Jobs\ProcessThirdPartyMissedFeedRecordsJob;
use App\Jobs\ProcessThirdPartyFeedRecordsJob;

use App\Repositories\RawFeedEmailRepo;
use App\Repositories\EtlPickupRepo;
use App\DataModels\ProcessingRecord;
use Exception;

class ProcessFeedRecords extends Command
{

    use DispatchesJobs, PreventOverlapping;
    const NAME_BASE = 'FeedProcessing';

    private $pickupRepo;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:process {party} {--feed=} {--startChars=} {--rerun=} {--runtime-threshold=default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process provided feed records.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EtlPickupRepo $pickupRepo) {
        $this->pickupRepo = $pickupRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        $party = (int)$this->argument('party');
        $runtime = $this->option('runtime-threshold');
        $tracking = str_random(16);
        $hoursBack = $this->option('rerun');

        if (1 === $party) {
            if (!$feedId) {
                throw new Exception("First party feeds needs a feed id specified.");
            }

            $feedId = $this->option('feed');
            $name = self::NAME_BASE . '-' . $feedId;
            
            if (!is_null($this->option('rerun'))) {
                $job = (new ProcessFirstPartyMissedFeedRecordsJob($name, $feedId, $hoursBack, $tracking, $runtime))->onQueue('RecordProcessing');
            }
            else {
                $startPoint = $this->getStartPoint($name);
                $job = (new ProcessFirstPartyFeedRecordsJob($feedId, $name, $startPoint, $tracking, $runtime))->onQueue('RecordProcessing');
            }
        }
        else {
            // Currently just 3

            if (!is_null($this->option('rerun'))) {
                // this will go after all third party records, regardless of letters
                $name = self::NAME_BASE . '-rerun';
                $job = (new ProcessThirdPartyMissedFeedRecordsJob($name, $hoursBack, $tracking, $runtime))->onQueue('RecordProcessing');
            }
            else {
                $startChars = $this->option('startChars');

                if (!$startChars) {
                    throw new Exception("Third party feeds require email start characters.");
                }
                if (!preg_match('/^\w+$/', $startChars)) {
                    throw new Exception("Start chars '$startChars' is not valid");
                }

                $name = self::NAME_BASE . '-' . $startChars;
                $startPoint = $this->getStartPoint($name);

                $job = (new ProcessThirdPartyFeedRecordsJob($startChars, $name, $startPoint, $tracking, $runtime))->onQueue('RecordProcessing');
            }
        }

        $this->dispatch($job);
    }


    private function getStartPoint($name) {
        try {
            return $this->pickupRepo->getLastInsertedForName($name);
        }
        catch(Exception $e) {
            // Create a new listing.
            return 1;
        }
    }

}
