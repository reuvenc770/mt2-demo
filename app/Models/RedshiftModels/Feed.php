<?php

namespace App\Models\RedshiftModels;

use Illuminate\Database\Eloquent\Model;

class Feed extends Model
{
    protected $connection = 'redshift';
    public $timestamps = false;
    protected $guarded = [];
}