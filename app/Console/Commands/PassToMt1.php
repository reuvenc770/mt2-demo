<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\DataProcessingJob;

class PassToMt1 extends Command
{
    use DispatchesJobs;

    private $entities = ['email_list', 'user', 'EspAdvertiserJoin', 'link'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mt1Export {entity}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Harmonize data between MT2 and MT1 (in case the latter needs to be resucitated at some point).';

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
        $entity = $this->validateEntity($this->argument('entity'));
        $job = new DataProcessingJob($entity, str_random(16));
        $this->dispatch($job);
    }

    private function validateEntity($entityName) {
        if (!in_array($entityName, $this->entities)) {
            throw new \Exception("MT1 $entityName not found.");
        }

        return "Mt1Export-{$entityName}";
    }
}
