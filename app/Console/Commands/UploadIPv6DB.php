<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\UploadIPv6DBJob;

class UploadIPv6DB extends Command
{
    use DispatchesJobs;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'util:importIpv6 {--filename=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a GeoIP IPv6 location db file';

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
        $fileName = $this->option('filename');
        $job = new UploadIPv6DBJob($fileName);
        $this->dispatch($job);
    }
}
