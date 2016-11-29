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
        return $this-model->whereRaw(
            "user_id NOT IN (1842, 1843, 1846, 2002, 2071, 2133, 2242, 2257, 2278, 
            2342, 2384, 2385, 2508, 2528, 2545, 2546, 2548, 2595, 2604, 2606, 2621, 
            2625, 2626, 2629, 2642, 2643, 2644, 2664, 2680, 2681, 2682, 2683, 2684, 
            2751, 2766, 2786, 2812, 2851, 2889, 2909, 2954, 2960) 
            AND (
                (OrangeClient = 'Y' AND status = 'A') 
                OR 
                (status = 'A' AND user_id >= 2961) 
                OR
                (OrangeClient = 'Y' and status = 'D')
            )");

    }
}