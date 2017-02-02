<?php

namespace App\Console\Commands;

use App\Jobs\Traits\PreventJobOverlapping;
use Illuminate\Console\Command;
use Redis;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
class QueueManager extends Command
{
    use PreventJobOverlapping;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'manage:workers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $mapping = [ // should rename workers so this isnt needed
        "queues:AWeber" => "AWeber",
        "queues:default"       => "laravel-worker-group:*",
        "queues:fileDownloads" => "laravel-worker-fileDownloads:*",
        "queues:BlueHornet"    => "laravel-worker-BlueHornet:*",
        "queues:orphanage"     => "Daddy-Warbucks:*",
        "queues:Campaigner"    => "Campaigner:*",
        "queues:Publicators"   => "Publicators*",
        "queues:attribution"   => "attribution:*",
        "queues:filters"       => "filters:*",
        "queues:Bronto"        => "Bronto:*",
    ];
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
       $queues = Redis::command('keys', ["queues:*"]);
        $records = 0;
        $delayed = 0;
        $reserved = 0;
       foreach($queues as $queue) {

           if (Redis::command('EXISTS', [$queue])) {
               $records = Redis::command('LLEN', [$queue]) > 0;
           }

           if (Redis::command('EXISTS', [$queue])) {
               $delayed = Redis::command('LLEN', [$queue . ':delayed']) > 0;
           }

           if (Redis::command('EXISTS', [$queue])) {
               $reserved = Redis::command('LLEN', [$queue] . ':reserved') > 0;
           }

           if ($records || $delayed || $reserved) {
             if($this->jobCanRun($queue)){
                $this->toggleWorkersCommand($queue,"start");
                 $this->createLock($queue);
           } else {
                 $this->toggleWorkersCommand($queue,"stop");
                 $this->unlock($queue);
             }
       }

       }
    }

    private function toggleWorkersCommand($worker, $direction){
        $command = "supervisorctl {$direction} {$worker}";
        $process = new Process($command);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
