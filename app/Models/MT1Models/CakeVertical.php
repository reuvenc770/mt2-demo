<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class CakeVertical extends Model {
    protected $connection = 'mt1_data';
    protected $table = 'CakeVertical';
    protected $primaryKey = 'verticalID';

}