<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class From extends Model
{
    protected $fillable = ['id', 'from_line', 'approved', 'status'];
}
