<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services\MT1Services;

use App\Repositories\MT1Repositories\DataCleanseRepo;
use App\Services\ServiceTraits\PaginateMT1Db;

class DataCleanseService {
    use PaginateMT1Db;

    public $repo;

    public function __construct ( DataCleanseRepo $repo ) {
        $this->repo = $repo;
    }

    public function getAll ( $page , $count ) {
        return $this->getPaginatedJson( 'getAll' , $page , $count );
    }
}
