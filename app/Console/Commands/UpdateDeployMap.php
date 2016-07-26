<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\MapEspInternalIdToDeployId;

class UpdateDeployMap extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapping:deploys';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keeps a mapping of esp_internal_id and deploy_id.';

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
        $this->dispatch( new MapEspInternalIdToDeployId( str_random( 16 ) ) );
    }
}
