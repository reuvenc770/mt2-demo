<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class Esp extends Model {
    protected $connection = 'mt1mail';
    protected $table = 'ESP';
    protected $primaryKey = 'espID';
}
