<?php

namespace App\Console\Commands;

use App\Jobs\domainExpirationNotifcations;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
class SendDomainExpirationNotice extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:sendExpirationNotices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grabs campaigns from CSV, finds unsubs, and sends a file via FTP to Sprint.';

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
        //TODO i really want to extend commands to handle jobs themselves  i dislike having to create a job for every task we want async
        $job = new domainExpirationNotifcations();
        $this->dispatch( $job );
    }
}
