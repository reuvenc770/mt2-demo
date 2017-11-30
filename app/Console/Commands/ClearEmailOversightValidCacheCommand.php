<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\ClearEmailOversightValidCacheJob;

class ClearEmailOversightValidCacheCommand extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'emailOversight:clearCache {--m|minAge=15 : Minimum age of cached items in days. Default is records older than 15 days since last validated. } { --runtime-threshold=5m : Threshold for monitoring. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears the Email Oversight Validation Cache of valid records.';

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
        $minAge = $this->option( 'minAge' );
        $runtimeThreshold = $this->option( 'runtime-threshold' );

        if ( !is_numeric( $minAge ) || 0 >= (int)$minAge ) {
            $this->error( "'{$minAge}' is an invalid minimum age. Must be 1 day or more." );
        }

        $this->dispatch( \App::make( \App\Jobs\ClearEmailOversightValidCacheJob::class , [
            $minAge ,
            str_random( 16 ) ,
            $runtimeThreshold
        ] ) );
    }
}
