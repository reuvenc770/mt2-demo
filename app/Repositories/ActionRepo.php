<?php

namespace App\Repositories;

use App\Models\ActionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class ActionRepo {
  
    private $actionType;

    public function __construct(ActionType $actionType) {
        $this->actionType = $actionType;
    } 

    public function getActionId($actionName) {
        return $this->actionType->select('id')->where('name', $actionName)->get()[0]['id'];
    }
}