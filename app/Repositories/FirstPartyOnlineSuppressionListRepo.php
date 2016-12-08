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

    public function getListsForFeed($feedId) {
        $output = [];
        $lists = $this->model
                    ->where('feed_id', $feedId)
                    ->select('suppression_list_id')
                    ->get();

        foreach($lists as $list) {
            $output[] = $list->suppression_list_id;
        }

        return $output;
    }

}