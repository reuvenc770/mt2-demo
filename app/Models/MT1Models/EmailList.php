<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class EmailList extends Model {
    protected $connection = 'mt1_data';
    protected $table = 'email_list';
    protected $primaryKey = 'email_user_id';
    public $timestamps = false;
}
