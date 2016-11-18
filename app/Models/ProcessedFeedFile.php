<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProcessedFeedFile extends Model
{
    protected $guarded = [ '' ];
    protected $primaryKey = 'path';
}
