<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 8/3/16
 * Time: 3:58 PM
 */

namespace App\Services\MapStrategies;


use App\Services\Interfaces\IMapStrategy;

class UniqueProfileListProfileMapStrategy implements IMapStrategy
{
    //1 to 1 right now;
    public function map($record) {
        return $record;
    }

}