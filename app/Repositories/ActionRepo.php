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
    private $actionMap;

    public function __construct(ActionType $actionType) {
        $this->actionType = $actionType;
    } 

    public function getActionId($actionName) {
        return $this->actionType->select('id')->where('name', $actionName)->get()[0]['id'];
    }

    public function getMap() {
        if (isset($this->actionMap)) {
            return $this->actionMap;
        }
        else {
            $rawMap = $this->actionType->get();
            $this->actionMap = array();

            foreach($rawMap as $action) {
                $this->actionMap[$action['id']] = $action['name'];
            }

            return $this->actionMap;
        }
    }
}