<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Console\Traits\PreventOverlapping;
use App\Jobs\ProcessFeedRecordsJob;
use App\Repositories\RawFeedEmailRepo;
use App\Repositories\EtlPickupRepo;
use App\DataModels\ProcessingRecord;
use Exception;

class ProcessFeedRecords extends Command
{

    use DispatchesJobs, PreventOverlapping;
    const NAME_BASE = 'FeedProcessing';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feedRecords:process {party} {--feed=} {--startChars=} {--runtime-threshold=default}';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(RawFeedEmailRepo $rawRepo, EtlPickupRepo $pickupRepo) {
        $party = (int)$this->argument('party');
        $feedId = $this->option('feed') ?: null;
        $startChars = $this->option('startChars') ?: null;

        if (1 === $party && !$feedId) {
            throw new Exception("First party feeds needs a feed id specified.");
        }
        if (3 === $party && !$startChars) {
            throw new Exception("Third party feeds require email start characters.");
        }
        if (3 === $party && !preg_match('/^\w+$/', $startChars)) {
            throw new Exception("Start chars '$startChars' is not valid");
        }

        $name = self::NAME_BASE . '-' . ((1 === $party) ? $feedId : $startChars);

        try {
            $startPoint = $pickupRepo->getLastInsertedForName($name);
        }
        catch(Exception $e) {
            // Create a new listing.
            $startPoint = 1;
        }
        
        if (1 === $party) {
            $records = $rawRepo->getFirstPartyRecordsFromFeed($startPoint, $feedId);
        }
        elseif (3 === $party) {
            $records = $rawRepo->getThirdPartyRecordsWithChars($startPoint, $startChars);
        }
        
        // Create array of ProcessingRecords and get last id
        $users = [];
        $maxId = 0;

        foreach($records as $record) {
            $users[] = new ProcessingRecord($record);
            $maxId = $record->id;
        }
        
        if ($maxId > 0) {
            $job = (new ProcessFeedRecordsJob($party, $feedId, $users, $name, $maxId, str_random(16), $this->option('runtime-threshold')))->onQueue('RecordProcessing');
            $this->dispatch($job);
        }
    }
}
