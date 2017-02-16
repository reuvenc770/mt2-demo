<?php

namespace App\Console\Commands;

use App\Jobs\warnWhenUnsubOff;
use Illuminate\Console\Command;
use App\Jobs\domainExpirationNotifications;
class NotifySomeoneAboutSomething extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:{something} {someone}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'We have a lot of notification jobs lets have one command launch them all';

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
        //someone is not used right now but when needed its there.. like a good friend.
        switch ($this->argument('party')){
            case 'expireNotices':
                $job = new domainExpirationNotifications(str_random(16));
                $this->dispatch( $job );
                break;
            case 'unsubWarning':
                $job = new warnWhenUnsubOff(str_random(16));
                $this->dispatch( $job );
                break;
            default:
                $this->info("Sorry that something can't be sent to someone");
        }

    }
}
