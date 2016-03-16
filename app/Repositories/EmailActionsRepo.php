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

    public function insertAction($data) {
        $this->actions->insert($data);
    }
}