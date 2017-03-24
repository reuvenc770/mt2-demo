<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawDeliveredEmail extends Model
{
    protected $guarded = [];
    protected $connection = 'reporting_data';
}
