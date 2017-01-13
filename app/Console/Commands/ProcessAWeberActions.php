<?php

namespace App\Console\Commands;

use App\Jobs\AWeberActionImmigration;
use App\Models\AweberEmailActionsStorage;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class ProcessAWeberActions extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'processAWeberActions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $actions = AweberEmailActionsStorage::take(10000)->get();
        while (count($actions) > 0) {
            foreach ($actions as $chunk) {
                $this->info("Processing another chunk");
                $job = new AWeberActionImmigration($chunk, str_random(16));
                $this->dispatch($job);
            }

            $actions = AweberEmailActionsStorage::take(10000)->get();
        }
    }
}
