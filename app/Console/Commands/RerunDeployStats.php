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
    protected $signature = 'reports:rerunDeliverables {espName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rerun all stats-deficient deploys.';
    protected $espsWithQueues = ['BlueHornet', 'Campaigner', 'Publicators'];

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
        $espName = $this->argument('espName');
        $queue = in_array($espName, $this->espsWithQueues) ? $espName : "default";
        $pipe = 'rerun';

        echo "About to call reports:downloadDeliverables {$espName}:{$pipe} $queue" . PHP_EOL;
        $this->call('reports:downloadDeliverables', [
            'espName' => $espName . ':' . $pipe,
            'queueName' => $queue,
        ]);
        
    }
}
