<?php

namespace App\Console\Commands;

use App\Factories\APIFactory;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Jobs\RetrieveReports;
use Illuminate\Foundation\Bus\DispatchesJobs;
class GrabESPStats extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:downloadESP {espName}';
    protected $factory;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';


    /**
     * GrabESPStats constructor.
     * @param APIFactory $factory
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
        $date = Carbon::now()->subDay(5)->toDateString();
        $espName = $this->argument('espName');
        //Grab all the accounts for esp.
        $this->info('CRONNNN');
        //FOREACH ACCOUNT NUMBER FOR GIVEN ESP
        $this->dispatch(new RetrieveReports($espName, "BH001", $date));
    }
}
