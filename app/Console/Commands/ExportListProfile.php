<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ExportListProfileJob;

class ExportListProfile extends Command
{

    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:export {listProfileId}';
    const QUEUE = 'ListProfile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create list profile exports using the proper suppression lists.';

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
    public function handle() {
        $listProfileId = $this->argument('listProfileId');
        $job = (new ExportListProfileJob($listProfileId, str_random(16)))->onQueue(self::QUEUE);
        $this->dispatch($job);
    }
}
