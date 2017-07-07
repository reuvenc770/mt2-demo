<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Repositories\ListProfileFlatTableRepo;
use App\Repositories\ThirdPartyEmailStatusRepo;
use App\Repositories\AttributionRecordTruthRepo;
use App\Repositories\FirstPartyRecordDataRepo;

class NewActionsService {
    protected $lpFlatRepo;
    protected $emailStatusRepo;
    protected $recordTruthRepo;
    private $firstPartyRepo;

    public function __construct ( ListProfileFlatTableRepo $lpFlatRepo , 
        ThirdPartyEmailStatusRepo $emailStatusRepo , 
        AttributionRecordTruthRepo $recordTruthRepo,
        FirstPartyRecordDataRepo $firstPartyRepo) {

        $this->lpFlatRepo = $lpFlatRepo;
        $this->emailStatusRepo = $emailStatusRepo;
        $this->recordTruthRepo = $recordTruthRepo;
        $this->firstPartyRepo = $firstPartyRepo;
    }

    public function updateThirdPartyEmailStatuses ( $dateRange ) {
        $this->runUpdateFromListProfileFlatTable( 'emailStatusRepo' , 'getThirdPartyEmailStatusExtractQuery' , $dateRange );
    }

    public function updateAttributionRecordTruths ( $dateRange ) {
        $this->runUpdateFromListProfileFlatTable( 'recordTruthRepo' , 'getRecordTruthsExtractQuery' , $dateRange );
    }

    public function updateFirstPartyEmailStatuses($dateRange) {
        $pdo = \DB::connection($this->lpFlatRepo->getConnection())->getPdo();
        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        $query = $this->lpFlatRepo->getFirstPartyActionStatusQuery();
        $statement = $pdo->prepare($query);
        $statement->execute([':start' => $dateRange['start'], ':end' => $dateRange['end']]);

        while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
            $this->firstPartyRepo->updateActionData($row);
        }

        $this->firstPartyRepo->cleanUpActions();
    }

    protected function runUpdateFromListProfileFlatTable ( $repoName , $extractMethod , $dateRange ) {
        $pdo = \DB::connection( $this->lpFlatRepo->getConnection() )->getPdo();

        $pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        $query = $this->lpFlatRepo->$extractMethod();

        $statement = $pdo->prepare( $query );

        $statement->execute( [ ':start' => $dateRange[ 'start' ] , ':end' => $dateRange[ 'end' ] ] );

        while( $row = $statement->fetch( \PDO::FETCH_ASSOC ) ) {
            $mappedRow = $this->$repoName->batchInsert( $row );
        }

        $this->$repoName->insertStored();
    }

}
