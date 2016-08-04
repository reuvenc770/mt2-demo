<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class Creative extends Model
{
    protected $connection = 'mt1_data';
    protected $table = 'creative';
    protected $primaryKey = 'creative_id';

}