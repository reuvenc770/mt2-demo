<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\FtpUser;

use Log;

class FtpUserRepo {
    public function __construct () {
    }

    public function save ( $credentials , $directory , $host = null , $serviceName = null ) {
        $status = FtpUser::create( [
            'username' => $credentials[ 'username' ] ,
            'password' => $credentials[ 'password' ] ,
            'directory' => $directory ,
            'host' => $host ?: '' ,
            'service' => $serviceName ?: ''
        ] );

        return $status;
    }
}
