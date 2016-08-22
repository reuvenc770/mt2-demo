<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\EmailClientInstanceRepo;

class EmailClientInstanceService {
    protected $repo;

    public function __construct ( EmailClientInstanceRepo $repo ) {
        $this->repo = $repo;
    }

    public function getMt1UniqueCountForClientAndDate ( $clientId , $date ) {
        return $this->repo->getMt1UniqueCountForClientAndDate( $clientId , $date );
    }

    public function getMt2UniqueCountForClientAndDate ( $clientId , $date ) {
        return $this->repo->getMt2UniqueCountForClientAndDate( $clientId , $date );
    }
}
