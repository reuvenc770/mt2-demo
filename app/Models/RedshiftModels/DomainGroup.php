<?php

namespace App\Models\RedshiftModels;

use Illuminate\Database\Eloquent\Model;

class DomainGroup extends Model
{
    protected $connection = 'redshift';
    public $timestamps = false;
    protected $guarded = [];
}