<?php
namespace App\Models\ModelTraits;
use Cache;
use Log;
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 4/28/16
 * Time: 2:29 PM
 */
trait ModelCacheControl
{
    public function getClassName(){
        Log::info(class_basename($this));
        return class_basename($this);
    }
    public static function bootModelCacheControl()
    {
        static::created(function($item) {
            Cache::tags($item->getClassName())->flush();
        });

        static::updated(function($item){
            Log::info($item->getClassName());
            Cache::tags($item->getClassName())->flush();
        });
    }
}