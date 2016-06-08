<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use App\Events\DeploysMissingDataFound;
use App\Repositories\DeployRecordRerunRepo;

class RerunDeployStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:rerunDeliverables {espName} {lookBack?} {queueName?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rerun all stats-deficient deploys.';

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
        $lookBack = $this->argument('lookBack') ? $this->argument('lookBack') : env('LOOKBACK', 5);
        $queue = (string) $this->argument('queueName') ? $this->argument('queueName') : "default";
        $pipe = 'rerun';
        $espName = $this->argument('espName');

        $this->call('reports:downloadDeliverables', [
            'espName' => $espName . ':' . $pipe,
            'lookBack' => $lookBack,
            'queueName' => $queue,
        ]);
        
    }
}
