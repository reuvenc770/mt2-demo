<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Services;

use App\Repositories\FtpUserRepo;

use Log;

class FtpUserService {
    protected $ftpUserRepo;

    public function __construct ( FtpUserRepo $ftpUserRepo ) {
        $this->ftpUserRepo = $ftpUserRepo;
    }

    public function save ( $credentials , $directory , $host = null , $serviceName = null ) {
        return $this->ftpUserRepo->save( $credentials , $directory , $host , $serviceName );
    }
}
