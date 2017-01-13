<?php

namespace App\Models\RedshiftModels;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $connection = 'redshift';
    public $timestamps = false;
    protected $guarded = [];
}
