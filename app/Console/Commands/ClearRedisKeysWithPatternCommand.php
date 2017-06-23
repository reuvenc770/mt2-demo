<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Redis;

class ClearRedisKeysWithPatternCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:patternClear {--P|pattern= : Pattern to search by for key } {--c|confirm : Confirm key deletion. }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Searches for keys using provided pattern and deletes them. Use confirm option to get confirmations for each key.';

    protected $pattern = '';
    protected $confirmDeletions = false;

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
        $this->processOptions();

        foreach ( REDIS::connection( 'cache' )->keys( '*' ) as $key ) {
            if ( strpos( $key , $this->pattern ) !== false ) {
                if ( !$this->confirmDeletions || $this->confirm( "Do you wish to delete '{$key}'?" ) ) {
                    REDIS::connection( 'cache' )->del( $key );
                }
            }
        }
    }

    protected function processOptions () {
        if ( is_null( $this->option( 'pattern' ) ) ) {
            $this->error( 'Key pattern is required.' );
        } else {
            $this->pattern = $this->option( 'pattern' );
        }

        $this->confirmDeletions = $this->option( 'confirm' );
    }
}
