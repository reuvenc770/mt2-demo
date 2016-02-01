<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class EspUiController extends Controller
{
    public function index () {
        return view( 'pages.esp.esp-index' );
    }

    public function add () {
        return view( 'pages.esp.esp-add' );
    }
}
