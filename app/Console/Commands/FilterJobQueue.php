<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use Redis;

class FilterJobQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'util:filter {string} {list}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Filters named redis list for values containing string';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
    
        Redis::pipeline(function ($pipe) {
            $string = $this->argument('string');
            $fromList = $this->argument('list');
            $toList = "TEMPORARY_FILTERING_LIST";

            $values = Redis::lrange($fromList, 0, -1);

            foreach($values as $value) {
                if (!preg_match("/$string/", $value)) {
                    Redis::rpush($toList, $value);
                }
            }

            Redis::rename($toList, $fromList);
        });
    }
    
}