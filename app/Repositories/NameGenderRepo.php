<?php

namespace App\Repositories;

use App\Models\NameGender;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

/**
 *
 */
class NameGenderRepo {
  
    private $model;

    public function __construct(NameGender $model) {
        $this->model = $model;
    }

    public function getGender($firstName) {
        $firstName = strtolower($firstName);
        $result = $this->model->where('first_name', $firstName)->first();

        if ($result) {
            return $result->gender;
        }
        else {
            return 'UNK';
        }
    }

}