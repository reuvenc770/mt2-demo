<?php

namespace App\Services;

use App\Repositories\RepoInterfaces\ICanonicalDataSource;
use App\Repositories\EtlPickupRepo;

class DataValidationService {

    private $trustedSourceRepo;
    private $reposToCheck;
    private $etlPickupRepo;
    private $pickupName = '';
    const ROW_COUNT_LIMIT = 10000;
    
    public function __construct(ICanonicalDataSource $trustedSourceRepo, EtlPickupRepo $etlPickupRepo, array $reposToCheck) {
        $this->trustedSourceRepo = $trustedSourceRepo;
        $this->reposToCheck = $reposToCheck;
        $this->etlPickupRepo = $etlPickupRepo;
    }

    public function runComparison($type) {
        echo "Running comparison" . PHP_EOL;
        if ('value' === $type) {
            // get_class returns something like "App\Repositories\EmailRepo"
            $this->pickupName = explode('\\', get_class($this->trustedSourceRepo))[2] . '-' . $type;
            $this->runValueComparisonCheck();
        }
        elseif ('exists' === $type) {
            $this->pickupName = explode('\\', get_class($this->trustedSourceRepo))[2] . '-' . $type;
            $this->runDataExistenceCheck();
        }
        else {
            Log::error("Data Consistency Validation type $type does not exist.");
        }
    }

    private function runValueComparisonCheck() {
        $startPoint = $this->etlPickupRepo->getLastInsertedForName($this->pickupName);
        // need to process this startPoint for compound values ..... but this is an int!

        $endPoint = $this->trustedSourceRepo->maxId();

        echo "Starting at $startPoint and ending at $endPoint" . PHP_EOL;

        while ($this->trustedSourceRepo->lessThan($startPoint, $endPoint)) {

            $segmentEnd = $this->trustedSourceRepo->nextNRows($startPoint, self::ROW_COUNT_LIMIT); // a nullable result
            $segmentEnd = $segmentEnd ?: $endPoint;

            echo "Running the curent segment between $startPoint and $segmentEnd" . PHP_EOL;

            foreach ($this->reposToCheck as $repo) {
                $newRows = $this->trustedSourceRepo->compareSourcesWithField($repo->getTableName(), $startPoint, $segmentEnd);

                if (count($newRows)) {
                    $repo->updateRowValues($newRows);
                }
            }

            $startPoint = $segmentEnd;
        }

        $this->etlPickupRepo->updatePosition($this->pickupName, 0); // Need to keep values updated, so resetting to start
    }


    private function runDataExistenceCheck() {

        $startPoint = $this->etlPickupRepo->getLastInsertedForName($this->pickupName);
        $endPoint = $this->trustedSourceRepo->maxId();

        echo "Starting at $startPoint and ending at $endPoint" . PHP_EOL;

        while ($startPoint < $endPoint) {

            $segmentEnd = $this->trustedSourceRepo->nextNRows($startPoint, self::ROW_COUNT_LIMIT);
            $segmentEnd = $segmentEnd ?: $endPoint;

            echo "Running the curent segment between $startPoint and $segmentEnd" . PHP_EOL;

            foreach ($this->reposToCheck as $repo) {
                $newRows = $this->trustedSourceRepo->compareSources($repo->getTableName(), $startPoint, $segmentEnd);

                if (count($newRows)) {
                    $repo->addNewRows($newRows);
                }
            }

            $startPoint = $segmentEnd;
        }

        $this->etlPickupRepo->updatePosition($this->pickupName, $endPoint);
    }
}