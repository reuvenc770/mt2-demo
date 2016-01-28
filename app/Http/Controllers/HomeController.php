<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/28/16
 * Time: 2:09 PM
 */

namespace App\Http\Controllers;

use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
class HomeController extends Controller
{
    public function home() {
        return view('layout.app');
    }


    public function fuck(){

        $role = Sentinel::findRoleById(2);
        $role->users()->attach(Sentinel::getUser());
    }
}