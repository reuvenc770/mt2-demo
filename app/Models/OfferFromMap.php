<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferFromMap extends Model {
  
    protected $guarded = ['id'];
    protected $connection = "reporting_data";
    public $timestamps = false;

}