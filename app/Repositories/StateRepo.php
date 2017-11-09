<?php

namespace App\Repositories;

 use AdrianMejias\States\States;

 class StateRepo {

    private $model;

    public function __construct(States $model) {
        $this->model = $model;
    }

    public function checkAbbrevExists($abbrev) {
        return $this->model->where('iso_3166_2', $abbrev)->count() > 0;
    }

    public function convertFullNameToAbbrev($fullName) {
        $find = $this->model->where('name', $fullName)->first();

        if (!$find) {
            return '';
        }
        else {
            return $find->iso_3166_2;
        }
    }
 }