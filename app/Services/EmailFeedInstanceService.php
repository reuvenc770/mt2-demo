<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\EmailFeedInstanceRepo;

class EmailFeedInstanceService {
    protected $repo;

    public function __construct ( EmailFeedInstanceRepo $repo ) {
        $this->repo = $repo;
    }

    public function getMt1UniqueCountForFeedAndDate ( $feedId , $date ) {
        return $this->repo->getMt1UniqueCountForFeedAndDate( $feedId , $date );
    }

    public function getMt2UniqueCountForFeedAndDate ( $feedId , $date ) {
        return $this->repo->getMt2UniqueCountForFeedAndDate( $feedId , $date );
    }

    public function getRecordCountForSource ( $search ) {
        return $this->repo->getRecordCountForSource( $search );
    } 

    public function updateSourceUrlCounts ( $startDate , $endDate ) {
        $this->repo->clearCountForDateRange( $startDate , $endDate );

        $records = $this->repo->getInstancesForDateRange( $startDate , $endDate );

        $totalCount = 0;

        $countList = [];
        foreach ( $records->cursor() as $currentRecord ) {
            $index = "{$currentRecord[ 'feed_id' ]}_{$currentRecord[ 'source_url' ]}_{$currentRecord[ 'capture_date' ]}";
            if ( !array_key_exists( $index , $countList ) ) {
                $countList[ $index ] = [
                    'feed_id' => $currentRecord[ 'feed_id' ] ,
                    'source_url' => $currentRecord[ 'source_url' ] ,
                    'capture_date' => $currentRecord[ 'capture_date' ] ,
                    'count' => 0
                ];
            }
            
            $totalCount++;
            $countList[ $index ][ 'count' ]++;
        }

        $this->repo->saveSourceCounts( $countList );

        return $totalCount;
    }  
}
