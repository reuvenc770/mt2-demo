<?php

namespace App\Console\Commands;

use App\Jobs\AWeberUpdateLists;
use Illuminate\Console\Command;
use App\Repositories\EspApiAccountRepo;
use Illuminate\Foundation\Bus\DispatchesJobs;

class UpdateAWeberLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    use DispatchesJobs;
    protected $signature = 'aweber:updateAWeberLists';
    protected $espRepo;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'look for new aweber lists for each account';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(EspApiAccountRepo $espRepo)
    {
        parent::__construct();
        $this->espRepo = $espRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $espAccounts = $this->espRepo->getAccountsByESPName("AWeber");
        foreach($espAccounts as $espAccount){
            $job = new AWeberUpdateLists($espAccount->id,str_random(16));
            $this->dispatch($job);
        }
    }
}
