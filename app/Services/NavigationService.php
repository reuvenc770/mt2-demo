<?php

namespace App\Services;

use Route;

class NavigationService {
    protected $route;

    public function __construct ( Route $route ) {
        $this->route = $route;
    }


    public function getMenu () {
        echo( 'stuff' );
    }
}
