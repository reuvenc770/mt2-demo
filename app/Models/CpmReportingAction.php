<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CpmReportingAction extends Model
{
    public static function boot () {
       parent::boot();

       static::saving( function ( $model ) {
          return false;
       } );
    }
}
