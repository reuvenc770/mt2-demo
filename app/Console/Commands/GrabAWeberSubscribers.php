<?php

namespace App\Console\Commands;

use App\Jobs\AggregateAWeberSubscribers;
use App\Models\AWeberList;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\EspApiAccountService;

class GrabAWeberSubscribers extends Command
{
    use DispatchesJobs;
    use InteractsWithQueue;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'grabAWeberSubscribers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Grab Subscriber Data from AWeber Data from All Active Lists';

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
    public function handle( EspApiAccountService $espServ )
    {
        $lists = AWeberList::where("is_active",1)->get();
        foreach($lists as $list) {
            if ( $espServ->statsEnabledForAccount( $list->esp_account_id ) ) {
                $job = (new AggregateAWeberSubscribers($list, str_random(16)))->onQueue("AWeber");
                $this->dispatch($job);
            } else {
                $this->info( 'AWeber stats disabled for account ' . $list->esp_account_id . '. Aborting subscriber pull for list ' . $list->internal_id);
            }
        }
    }
}
