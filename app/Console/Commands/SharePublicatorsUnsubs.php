<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SharePublicatorsUnsubsJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\EspRepo;

class SharePublicatorsUnsubs extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suppression:exportPublicators {lookback}';
    protected $lookback;
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send all Publicators unsubs from the past 
    $lookback days to all other Publicators accounts.';

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
    public function handle(EspRepo $esp) {
        $espId = $esp->getEspByName('Publicators')->id;
        $this->lookback = $this->argument('lookback') ? $this->argument('lookback') : 1;
        $job = new SharePublicatorsUnsubsJob($espId, $this->lookback, str_random(16));
        $this->dispatch($job);
    }
}
