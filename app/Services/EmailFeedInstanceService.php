<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\EmailFeedInstanceRepo;
use App\Repositories\SourceUrlCountRepo;

class EmailFeedInstanceService {
    protected $repo;
    private $countRepo;

    public function __construct ( EmailFeedInstanceRepo $repo, SourceUrlCountRepo $countRepo ) {
        $this->repo = $repo;
        $this->countRepo = $countRepo;
    }

    public function getRecordCountForSource ( $search ) {
        return $this->countRepo->getRecordCountForSource( $search );
    } 

    public function updateSourceUrlCounts ( $startDate , $endDate ) {
        $totalCount = 0;

        try {
            $records = $this->repo->getSourceUrlCountsForDates($startDate, $endDate);
            $totalCount = count($records);
            $this->countRepo->saveSourceCounts($records);
        } catch (\Exception $e){
            throw $e; //let's get it up to the job so it can be properly tracked
        }

        return $totalCount;
    }  
}
