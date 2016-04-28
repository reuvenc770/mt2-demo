<?php
namespace App\Models\ModelTraits;
use Cache;
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/28/16
 * Time: 2:29 PM
 */
trait ModelCacheControl
{
    public static function bootModelCacheControl()
    {
        static::created(function($item) {
            Cache::tags($item->getClassName())->flush();
        });

        static::updated(function($item){
            Cache::tags($item->getClassName())->flush();
        });
    }
}