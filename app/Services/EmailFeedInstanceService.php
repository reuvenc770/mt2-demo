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
        try {
            $this->countRepo->clearCountForDateRange( $startDate , $endDate );

            $records = $this->repo->getInstancesForDateRange( $startDate , $endDate );

            $totalCount = 0;

            $countList = [];
            foreach ($records->cursor() as $currentRecord) {
                $index = "{$currentRecord[ 'feed_id' ]}_{$currentRecord[ 'source_url' ]}_{$currentRecord[ 'subscribe_date' ]}";
                if (!array_key_exists($index, $countList)) {
                    $countList[$index] = [
                        'feed_id' => $currentRecord['feed_id'],
                        'source_url' => $currentRecord['source_url'],
                        'subscribe_date' => $currentRecord['subscribe_date'],
                        'count' => 0
                    ];
                }

                $totalCount++;
                $countList[$index]['count']++;
            }

            $this->countRepo->saveSourceCounts($countList);
        } catch (\Exception $e){
            throw $e;//lets get it up to the job so it can be properly tracked
        }

        return $totalCount;
    }  
}
