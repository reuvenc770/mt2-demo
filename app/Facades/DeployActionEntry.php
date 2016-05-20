<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 2/22/16
 * Time: 11:10 AM
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DeployActionEntry extends Facade
{
    protected static function getFacadeAccessor() { return 'DeployActionEntry'; }
}
