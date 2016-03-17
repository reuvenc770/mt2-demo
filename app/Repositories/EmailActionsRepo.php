<?php

namespace App\Repositories;

use App\Models\EmailAction;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use Carbon\Carbon;;

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

    public function pullActionsInLast($lookback) {
        // any way to make this unbuffered or chunk, perhaps?
        return $this->actions->where('created_at', '>=', Carbon::today()->subDays($lookback))->get();
    }
}