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
        $espAccount->where("updated_at", '>=',$date)->where("status",2)->update(["status"=>0]);
        \Cache::tags("EspAccount")->flush();
    }
}
