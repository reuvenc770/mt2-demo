<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 10/31/16
 * Time: 12:09 PM
 */

namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class SlackLevel extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'maknz.slack.level'; }


}