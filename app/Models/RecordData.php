<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecordData extends Model {
    
    protected $table = 'record_data';
    protected $primaryKey = 'email_id';
    protected $guarded = [];
}
