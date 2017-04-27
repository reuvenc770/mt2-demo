<?php
namespace App\Services;
use Aws\S3\S3Client;
use Aws\S3\MultipartUploader;
use Aws\Exception\MultipartUploadException;
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
    const SINGLE_UPLOAD_MAX_SIZE_BYTES = 1073741824; // 1 GiB. 5GiB is currently the max in a single upload.
    private $tries = 1;
    const MAX_TRIES = 5;

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
        return $this->write($resource, $stopPoint);
    }

    public function extractAll() {
        $resource = $this->repo->extractAllForS3();
        return $this->write($resource, 0);   
    }

    public function specialExtract($data) {
        $resource = $this->repo->specialExtract($data);
        return $this->write($resource, 0);
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
        
        $rowsProcessed = 0;

        while($row = $statement->fetch(PDO::FETCH_OBJ)) {
            $tmpNextId = $this->strat->__invoke($row);
            $nextId = max($tmpNextId, $nextId);

            $mappedRow = $this->repo->mapForS3Upload($row);
            $this->batch($this->filePath, $mappedRow);
            $rowsProcessed++;
        }

        $this->pickupRepo->updatePosition($this->entity . '-s3', $nextId);
        $this->writeBatch($this->filePath);
        
        return $rowsProcessed;
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
        if (filesize($this->filePath) > self::SINGLE_UPLOAD_MAX_SIZE_BYTES) {

            $uploader = new MultipartUploader($this->s3Client, $this->filePath, [
                'key' => "{$this->entity}.csv",
                'bucket' => config('aws.s3.fileUploadBucket'),
                'acl' => 'public-read'
            ]);

            do {
                try {
                    $result = $uploader->upload();
                    echo "Multi-part upload for {$this->entity} complete" . PHP_EOL;
                } 
                catch (MultipartUploadException $e) {
                    if ($this->tries <= self::MAX_TRIES) {
                        $this->tries++;

                        echo "Upload for $entity failed with {$e->getMessage()}. Retrying {$this->tries}." . PHP_EOL;
                        $uploader = new MultipartUploader($s3Client, $source, [
                            'state' => $e->getState(),
                        ]);
                    }
                    else {
                        throw new Exception("Multi-part upload for $entity failed completely with {$e->getMessage()}.");
                    }
                }
            } while (!isset($result));

        }
        else {
            $result = $this->s3Client->putObject([
                'Bucket' => config('aws.s3.fileUploadBucket'),
                'Key' => "{$this->entity}.csv",
                'SourceFile' => $this->filePath,
            ]);
        }

        $this->redshiftRepo->clearAndReloadEntity($this->entity);

        // And then delete the file
        File::delete($this->filePath);

        return true;
    }


    private function batch($fileName, $row) {
        if ($this->rowCount >= self::WRITE_THRESHOLD) {
            // Workaround for File not appending to new line and pgsql COPY failing on empty line
            $this->rows[] = '';
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
        File::append($this->filePath, $string); 
    }
}
