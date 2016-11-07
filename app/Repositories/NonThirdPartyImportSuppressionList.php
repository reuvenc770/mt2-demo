<?php

namespace App\Repositories;

use App\Models\NonThirdPartyImportSuppressionList;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class NonThirdPartyImportSuppressionListRepo {
  
    private $model;

    public function __construct(NonThirdPartyImportSuppressionList $model) {
        $this->model = $model;
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