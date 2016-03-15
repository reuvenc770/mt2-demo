<?php

namespace App\Repositories;

use App\Models\EmailAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class EmailActionsRepo {
  
    private $actions;

    public function __construct(EmailAction $actions) {
        $this->actions = $actions;
    } 

    public function getActionId($actionName) {
        return $this->actions->select('id')->where('name', $actionName)->get();
    }
}