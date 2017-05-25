<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\S3RedshiftExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

class S3RedshiftExport extends Command
{
    use DispatchesJobs;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listprofile:dataEtl {--all} {--runtime-threshold=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Export tables to s3 and redshift";

    private $entities = ['EmailDomain', 'EmailFeedAssignment', 'Email', 'Feed', 'ListProfileFlatTable', 
    'RecordData', 'SuppressionGlobalOrange', 'SuppressionListSuppression', 'DomainGroup', 'Client', 'FirstPartyRecordData'];

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
    public function handle() {
        S3RedshiftExportJob::clearNotificationTally();

        $version = $this->option('all') ? 1 : 0;
        foreach ($this->entities as $entity) {
            $job = new S3RedshiftExportJob($entity, $version, str_random(16), $this->option('runtime-threshold'));
            $this->dispatch($job);
        }
    }
}
