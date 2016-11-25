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
}
