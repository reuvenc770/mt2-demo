<?php

namespace App\Console\Commands;

use App\Jobs\domainExpirationNotifications;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class DomainExpirationNotification extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Emails about Expired Domains';

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
        $job = new domainExpirationNotifications(str_random(16));
        $this->dispatch($job);
    }
}
