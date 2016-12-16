<?php

namespace App\Repositories\MT1Repositories;

use App\Models\MT1Models\User;
use App\Models\MT1Models\LiveUser;
use DB;
use App\Repositories\RepoInterfaces\Mt1Import;

class UserRepo implements Mt1Import {
    protected $model;
    private $liveModel;

    public function __construct (User $model, LiveUser $liveModel) {
        $this->model = $model;
        $this->liveModel = $liveModel;
    }

    public function pullForSync($lookback) {
        return $this->model->whereRaw(
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

    public function insertToMt1($data) {
        $this->liveModel->updateOrCreate(['user_id' => $data['user_id']], $data);
    }
}