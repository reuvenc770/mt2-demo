<?php
/**
 * @author Adam Chin <achin@zetaglobal.net>
 */

namespace App\Services;

use App\Repositories\RawFeedEmailRepo;

class FeedApiService {
    protected $repo;

    public function __construct ( RawFeedEmailRepo $repo ) {
        $this->repo = $repo;
    }

    public function ingest ( $record ) {

    }
}
