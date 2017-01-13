<?php

namespace App\Models\RedshiftModels;

use Illuminate\Database\Eloquent\Model;

class EmailDomain extends Model
{
    protected $connection = 'redshift';
    public $timestamps = false;
    protected $guarded = [];
}