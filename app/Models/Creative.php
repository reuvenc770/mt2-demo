<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Creative extends Model
{
    protected $fillable = ['id', 'name', 'file_name', 'creative_html', 'approved', 'status'];
}
