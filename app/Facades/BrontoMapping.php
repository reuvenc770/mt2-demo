<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/24/17
 * Time: 10:54 AM
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class BrontoMapping extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'BrontoMapping';
    }
}
