<?php

namespace App\Repositories;

use App\Models\ClientFeed;
use DB;

class ClientFeedRepo {

    protected $feed;

    public function __construct ( ClientFeed $feed ) {
        $this->feed = $feed;
    }

    public function getFeedFromPassword($password) {
        return $this->feed
                    ->where('password', $password)
                    ->firstOrFail();
    }

}
