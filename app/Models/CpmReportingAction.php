<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpmReportingAction extends Model
{
    protected $connection = "reporting_data";

    public static function boot () {
       parent::boot();

       static::saving( function ( $model ) {
          return false;
       } );
    }
}
