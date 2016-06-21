<?php

namespace App\Services\Interfaces;

interface IEtl {

    // transform() is internal to the class
    public function extract($lookback);
    public function load();
}