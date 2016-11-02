<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuppressionListType extends Model {
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $connection = 'suppression';
}