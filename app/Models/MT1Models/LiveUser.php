<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class LiveUser extends Model
{
    protected $connection = 'legacy_data_sync';
    protected $table = 'user';
    protected $primaryKey = 'user_id';
    public $timestamps = false;

}