<?php

namespace App\Services\RedshiftDataValidationStrategies;

use DB;
use PDO;

class SuppressionRedshiftDataValidation {
    
    private $cmpRepo;
    private $redshiftRepo;
    private $lookback;

    public function __construct($cmpRepo, $redshiftRepo) {
        $this->cmpRepo = $cmpRepo;
        $this->redshiftRepo = $redshiftRepo;
    }

    public function test($lookback) {
        $this->lookback = $lookback;
        // These will check _before_ a certain date.
        // Today's data will likely be out of sync
        $cmpCount = $this->cmpRepo->getCount($lookback);
        $rsCount = $this->redshiftRepo->getCount($lookback);

        return $cmpCount !== $rsCount;
    }

    public function fix() {
        $pdo = DB::connection($this->cmpRepo->getConnection())->getPdo();
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        $query = $this->cmpRepo->getAllQuery($this->lookback);
        $stmt = $pdo->prepare($query);
        $stmt->execute();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->redshiftRepo->insertIfNot($row);
        }
    }
}