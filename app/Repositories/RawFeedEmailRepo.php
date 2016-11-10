<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Repositories;

use App\Models\RawFeedEmail;
use App\Models\RawFeedEmailFailed;

class RawFeedEmailRepo {
    protected $rawEmail;
    protected $failed;

    public function __construct ( RawFeedEmail $rawEmail ) {
        $this->rawEmail = $rawEmail;
    }

    static public function logFailure ( $errors , $fullUrl , $referrerIp , $feedId = 0 ) {
        RawFeedEmailFailed::create( [
            'errors' => json_encode( $errors ) ,
            'url' => $fullUrl ,
            'ip' => $referrerIp ,
            'feed_id' => $feedId
        ] );
    }
}
