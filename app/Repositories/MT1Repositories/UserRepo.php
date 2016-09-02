<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\User;
use DB;

class UserRepo {
    protected $model;

    public function __construct (User $model) {
        $this->model = $model;
    }

    public function pullForSync($lookback) {
        return $this->model
                    ->where(function($query) { 
                        $query->where('status', 'A')
                              ->where('OrangeClient', 'Y');
                    })
                    ->orWhere(function ($query) {
                        $query->where('status', 'A')
                              ->where('user_id', '>=', 2961);
                    })
                    ->orWhere(function ($query) {
                        $query->where('status', 'D')
                              ->where('OrangeClient', 'Y');
                    });

    }
}