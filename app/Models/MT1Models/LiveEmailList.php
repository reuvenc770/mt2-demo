<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class LiveEmailList extends Model {
    protected $connection = 'legacy_data_sync';
    protected $table = 'email_list';
    protected $primaryKey = 'email_user_id';
    public $timestamps = false;
}
