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
        $result = $this
                ->feed
                ->select('status')
                ->where('id', $id)
                ->where('party', 3) // third party only, otherwise ignore
                ->first();

        if ($result) {
            return $result->status === 'Active';
        }
        return false;
    }

    public function getMaxFeedId() {
        return (int)$this->feed->orderBy('id', 'desc')->first()['id'];
    }

    public function insert($data) {
        $this->feed->insert($data);
    }

    public function updateOrCreate($data) {
        $this->feed->updateOrCreate(['id' => $data['id']], $data);
    }

}