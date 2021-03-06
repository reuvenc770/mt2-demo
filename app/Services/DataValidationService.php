<?php

namespace App\Services;

use App\Repositories\RepoInterfaces\ICanonicalDataSource;
use App\Repositories\EtlPickupRepo;
use Log;
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
        $count = 0;
        if ('value' === $type) {
            // get_class returns something like "App\Repositories\EmailRepo"
            $this->pickupName = explode('\\', get_class($this->trustedSourceRepo))[2] . '-' . $type;
            $count = $this->runValueComparisonCheck();
        }
        elseif ('exists' === $type) {
            $this->pickupName = explode('\\', get_class($this->trustedSourceRepo))[2] . '-' . $type;
            $count = $this->runDataExistenceCheck();
        }
        else {
            Log::error("Data Consistency Validation type $type does not exist.");
        }

        return $count;
    }

    private function runValueComparisonCheck() {
        $startPoint = $this->etlPickupRepo->getLastInsertedForName($this->pickupName);
        // need to process this startPoint for compound values ..... but this is an int!

        $endPoint = $this->trustedSourceRepo->maxId();

        echo "Starting {$this->pickupName} at $startPoint and ending at $endPoint" . PHP_EOL;

        while ($this->trustedSourceRepo->lessThan($startPoint, $endPoint)) {
            $segmentEnd = $this->trustedSourceRepo->nextNRows($startPoint, self::ROW_COUNT_LIMIT); // a nullable result
            $segmentEnd = $segmentEnd ?: $endPoint;

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
        $total = 0;
        $startPoint = $this->etlPickupRepo->getLastInsertedForName($this->pickupName);
        $endPoint = $this->trustedSourceRepo->maxId();
        echo "Starting at $startPoint and ending at $endPoint" . PHP_EOL;

        while ($startPoint < $endPoint) {

            $segmentEnd = $this->trustedSourceRepo->nextNRows($startPoint, self::ROW_COUNT_LIMIT);
            $segmentEnd = $segmentEnd ?: $endPoint;
            echo "Running the curent segment between $startPoint and $segmentEnd" . PHP_EOL;

            foreach ($this->reposToCheck as $repo) {
                $newRows = $this->trustedSourceRepo->compareSources($repo->getTableName(), $startPoint, $segmentEnd);

                $validNewRows = [];

                foreach ($newRows as $row) {
                    $validated = $this->trustedSourceRepo->validExists($row);
                    
                    if ($validated) {
                        $validNewRows[] = $validated;
                        $total++;
                    }
                }

                if (count($validNewRows)) {
                    $repo->addNewRows($validNewRows);
                }
            }

            $startPoint = $segmentEnd;
        }

        $this->etlPickupRepo->updatePosition($this->pickupName, $endPoint);

        return $total;
    }
}
