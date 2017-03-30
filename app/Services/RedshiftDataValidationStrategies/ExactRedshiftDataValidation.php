<?php

namespace App\Services\RedshiftDataValidationStrategies;
use App\Jobs\S3RedshiftExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Repositories\RepoInterfaces\IAwsRepo;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use App\Jobs\S3RedshiftExportJob;


class ExactRedshiftDataValidation {
    use DispatchesJobs;

    private $cmpRepo;
    private $redshiftRepo;

    public function __construct(IAwsRepo $cmpRepo, IRedshiftRepo $redshiftRepo) {
        $this->cmpRepo = $cmpRepo;
        $this->redshiftRepo = $redshiftRepo;
    }

    public function test($lookback) {
        $cmpCount = $this->cmpRepo->getCount();
        $rsCount = $this->redshiftRepo->getCount();
        return $cmpCount === $rsCount;
    }

    public function fix() {
        $cmpClass = explode('\\', get_class($this->cmpRepo))[2];
        $entity = str_replace('Repo', '', $cmpClass);
        $job = new S3RedshiftExportJob($entity, 1, str_random(16));

        $this->dispatch($job);
    }
}