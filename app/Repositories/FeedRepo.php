<?php

namespace App\Repositories;

use App\Models\Feed;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class FeedRepo {
  
    private $feed;

    public function __construct(Feed $feed) {
        $this->feed = $feed;
    }

    public function isActive($id) {
        return $this
                ->feed
                ->select('status')
                ->where('id', $id)
                ->get()[0]['status'] === 'Active';
    }

    public function getMaxFeedId() {
        return (int)$this->feed->orderBy('id', 'desc')->first()['id'];
    }

    public function insert($data) {
        $this->feed->insert($data);
    }

}