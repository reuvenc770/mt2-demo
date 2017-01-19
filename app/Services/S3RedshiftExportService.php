<?php
namespace App\Services;
use Aws\S3\S3Client;
use App\Repositories\RepoInterfaces\IAwsRepo;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use Storage;
use App\Repositories\EtlPickupRepo;

class S3RedshiftExportService {
    
    private $repo;
    private $s3Client;
    private $redshiftRepo;
    private $pickupRepo;
    private $filePath;
    const WRITE_THRESHOLD = 10000;
    private $rows;
    private $rowCount;

    private $strat;

    public function __construct(IAwsRepo $repo, S3Client $s3Client, IRedshiftRepo $redshiftRepo, EtlPickupRepo $pickupRepo, $entity, \Closure $strat) {
        $this->repo = $repo;
        $this->s3Client = $s3Client;
        $this->redshiftRepo = $redshiftRepo;
        $this->pickupRepo = $pickupRepo;
        $this->filePath = "/export/{$entity}.csv";
        $this->entity = $entity;

        $this->strat = $strat;
    }

    public function extract() {
        // this data should be cursor() -able
        $stopPoint = $this->pickupRepo->getLastInsertedForName($this->entity . '-s3');

        $resource = $this->repo->extractForS3Upload($stopPoint);
        $this->write($resource);
    }

    public function extractAll() {
        $resource = $this->repo->extractAllForS3();
        $this->write($resource);   
    }

    private function write($resource) {
        Storage::disk('local')->delete($this->filePath);

        $this->rowCount = 0;
        $nextId = $stopPoint;

        foreach($resource->cursor() as $row) {
            $tmpNextId = $this->strat->__invoke($row);
            $nextId = max($tmpNextId, $nextId);

            $mappedRow = $this->repo->mapForS3Upload($row);
            $this->batch($this->filePath, $mappedRow);
        }

        $this->pickupRepo->updatePosition($this->entity . '-s3', $nextId);
        $this->writeBatch($this->filePath);
    }

    public function load() {
        $result = $this->s3Client->putObject([
            'Bucket' => config('aws.s3.fileUploadBucket'),
            'Key' => "{$this->entity}.csv",
            'SourceFile' => storage_path('app') . $this->filePath,
        ]);

        $this->redshiftRepo->loadEntity($this->entity);

        // And then delete the file
        Storage::disk('local')->delete($this->filePath);
    }

    public function loadAll() {
        $result = $this->s3Client->putObject([
            'Bucket' => config('aws.s3.fileUploadBucket'),
            'Key' => "{$this->entity}.csv",
            'SourceFile' => storage_path('app') . $this->filePath,
        ]);

        $this->redshiftRepo->clearAndReloadEntity($this->entity);

        // And then delete the file
        Storage::disk('local')->delete($this->filePath);
    }


    private function batch($fileName, $row) {
        if ($this->rowCount >= self::WRITE_THRESHOLD) {
            $this->writeBatch($fileName,$disk);
            $this->rows = ['"' . implode('","', $row) . '"'];
            $this->rowCount = 1;
        } else {
            $this->rows[] = ('"' . implode('","', $row) . '"');
            $this->rowCount++;
        }
    }

    private function writeBatch($fileName) {
        $string = implode(PHP_EOL, $this->rows);
        Storage::disk('local')->append($fileName, $string);
    }
}