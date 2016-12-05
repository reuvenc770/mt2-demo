<?php

namespace App\Repositories;

use App\Models\FirstPartyOnlineSuppressionList;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class FirstPartyOnlineSuppressionListRepo {
  
    private $model;

    public function __construct(FirstPartyOnlineSuppressionList $model) {
        $this->model = $model;
    }

    public function getForFeedId($feedId) {
        return $this->model->where('feed_id', $feedId)->get();
    }

}