<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrontoIdMapping extends Model
{
    protected $guarded = [''];
    protected $connection = "reporting_data";
    public $timestamps = false;
}