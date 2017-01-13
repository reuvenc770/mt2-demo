<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/6/17
 * Time: 12:44 PM
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class AWeberEmailAction extends Facade{
    protected static function getFacadeAccessor() { return 'AWeberEmailAction'; }
}
