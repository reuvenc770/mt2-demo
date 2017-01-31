<?php
namespace App\Services;
use Aws\S3\S3Client;
use App\Repositories\RepoInterfaces\IAwsRepo;
use App\Repositories\RepoInterfaces\IRedshiftRepo;
use App\Repositories\EtlPickupRepo;
use DB;
use PDO;
use File;

class S3RedshiftExportService {
    
    private $repo;
    private $s3Client;
    private $redshiftRepo;
    private $pickupRepo;
    private $filePath;
    const WRITE_THRESHOLD = 10000;
    private $rows = [];
    private $rowCount;

    private $strat;

    public function __construct(IAwsRepo $repo, S3Client $s3Client, IRedshiftRepo $redshiftRepo, EtlPickupRepo $pickupRepo, $entity, \Closure $strat) {
        $this->repo = $repo;
        $this->s3Client = $s3Client;
        $this->redshiftRepo = $redshiftRepo;
        $this->pickupRepo = $pickupRepo;
        $this->filePath = config('misc.real_storage_path') . "/app/export/{$entity}.csv";
        $this->entity = $entity;

        $this->strat = $strat;
    }

    public function extract() {
        $stopPoint = $this->pickupRepo->getLastInsertedForName($this->entity . '-s3');

        $resource = $this->repo->extractForS3Upload($stopPoint);
        $this->write($resource, $stopPoint);
    }

    public function extractAll() {
        $resource = $this->repo->extractAllForS3();
        $this->write($resource, 0);   
    }

    private function write($resource, $stopPoint) {
        File::delete($this->filePath);
        File::put($this->filePath, '');

        $this->rowCount = 0;
        $nextId = $stopPoint;

        $pdo = DB::connection($this->repo->getConnection())->getPdo();

        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $statement = $pdo->prepare($resource->toSql());
        $statement->execute();

        while($row = $statement->fetch(PDO::FETCH_OBJ)) {
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
            'SourceFile' => $this->filePath,
        ]);

        $this->redshiftRepo->loadEntity($this->entity);

        // And then delete the file
        File::delete($this->filePath);
    }

    public function loadAll() {
        $result = $this->s3Client->putObject([
            'Bucket' => config('aws.s3.fileUploadBucket'),
            'Key' => "{$this->entity}.csv",
            'SourceFile' => $this->filePath,
        ]);

        $this->redshiftRepo->clearAndReloadEntity($this->entity);

        // And then delete the file
        File::delete($this->filePath);
    }


    private function batch($fileName, $row) {
        if ($this->rowCount >= self::WRITE_THRESHOLD) {
            $this->writeBatch($fileName);
            $this->rows = [$row];
            $this->rowCount = 1;
        } else {
            $this->rows[] = $row;
            $this->rowCount++;
        }
    }

    private function writeBatch($fileName) {
        $string = implode(PHP_EOL, $this->rows);
        // File (i.e. file_put_contents) will not append to the next newline, so this must be done manually.
        File::append($this->filePath, $string . PHP_EOL); 
    }
}
