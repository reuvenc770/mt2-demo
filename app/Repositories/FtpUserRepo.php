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
        $user = new FtpUser(); 

        $user->username = $credentials[ 'username' ];
        $user->password = $credentials[ 'password' ];
        $user->directory = $directory;

        if ( !is_null( $host ) ) { $user->host = $host; }
        if ( !is_null( $serviceName ) ) { $user->service = $serviceName; }

        return $user->save();
    }
}
