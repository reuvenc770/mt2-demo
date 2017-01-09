<?php

namespace App\Console\Commands;



use App\Jobs\ImportCsvStats;
use App\Models\ListProfileCombine;

use App\Repositories\ListProfileCombineRepo;

use App\Services\DeployService;
use DaveJamesMiller\Breadcrumbs\Exception;
use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;

class Inspire extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display an inspiring quote';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::emergency("I am an emergency");
        \Log::alert("I am an alert");
        \Log::critical("I am a crital alert");
        \Log::debug("I am a debug message");
        \Log::error("I am a error");
        \Log::info("I am information");
        \Log::warning("I am a warning");
        \Log::notice("I am a notice");
        trigger_error("Fatal error", E_USER_ERROR);
        }

}
