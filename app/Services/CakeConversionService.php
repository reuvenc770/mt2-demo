<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Services\Interfaces\IConversion;
use App\Repositories\CakeConversionRepo;

class CakeConversionService implements IConversion {
    protected $repo;

    public function __construct ( CakeConversionRepo $repo ) {
        $this->repo = $repo;
    }

    public function getByDate ( $dateRange = null ) {
        return $this->repo->getByDate( $dateRange );
    }

    public function getByDeployEmailDate ( $deployId , $emailId , $date  ) {
        return $this->repo->getByDeployEmailDate ( $deployId , $emailId , $date  );
    }
}
