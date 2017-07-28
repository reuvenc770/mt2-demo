<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/**
 * Routes are broken out into partials in app/Http/RoutePartials/. Please do not add more routes to this file.
 */
$basePath = app_path() . '/Http/RoutePartials/';

foreach ( Storage::disk( 'routePartials' )->files() as $filename ) {
    require( $basePath . $filename );
}
