<?php

namespace App\Console\Commands;

use App\Models\EspAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeactivateEspAccounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactivate:espAccounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deactivates all esp accounts that have been set to expire';

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
    public function handle(EspAccount $espAccount)
    {
        $date = Carbon::today()->toDateString();
        $espAccount->where("deactivation_date", '=', $date)->update(["enable_suppression" => 0, 'enable_stats' => 0]);
        \Cache::tags("EspAccount")->flush();
    }
}
