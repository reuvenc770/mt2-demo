<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSubjectMap extends Model {
  
    protected $guarded = ['id'];
    protected $connection = "reporting_data";
    public $timestamps = false;

}