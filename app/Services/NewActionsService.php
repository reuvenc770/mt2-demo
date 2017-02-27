<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Repositories\ListProfileFlatTableRepo;
use App\Repositories\ThirdPartyEmailStatusRepo;
use App\Repositories\AttributionRecordTruthRepo;

class NewActionsService {
    protected $lpFlatRepo;
    protected $emailStatusRepo;
    protected $recordTruthRepo;

    public function __construct ( ListProfileFlatTableRepo $lpFlatRepo , ThirdPartyEmailStatusRepo $emailStatusRepo , AttributionRecordTruthRepo $recordTruthRepo ) {
        $this->lpFlatRepo = $lpFlatRepo;
        $this->emailStatusRepo = $emailStatusRepo;
        $this->recordTruthRepo = $recordTruthRepo;
    }

    public function updateThirdPartyEmailStatuses ( $dateRange ) {
        $this->runUpdateFromListProfileFlatTable( 'emailStatusRepo' , 'getThirdPartyEmailStatusExtractQuery' , $dateRange );
    }

    public function updateAttributionRecordTruths ( $dateRange ) {
        $this->runUpdateFromListProfileFlatTable( 'recordTruthRepo' , 'getRecordTruthsExtractQuery' , $dateRange );
    }

    protected function runUpdateFromListProfileFlatTable ( $repoName , $extractMethod , $dateRange ) {
        $pdo = \DB::connection( $this->lpFlatRepo->getConnection() )->getPdo();

        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        $query = $this->lpFlatRepo->$extractMethod();

        $statement = $pdo->prepare( $query );

        $statement->execute( [ ':startDate' => $dateRange[ 'start' ] , ':endDate' => $dateRange[ 'end' ] ] );

        while( $row = $statement->fetch( \PDO::FETCH_ASSOC ) ) {
            $mappedRow = $this->$repoName->batchInsert( $row );
        }

        $this->$repoName->insertStored();
    }
}
