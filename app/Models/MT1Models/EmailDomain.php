<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class EmailDomain extends Model {
    protected $connection = 'mt1_data';
    protected $table = 'email_domains';
    protected $primaryKey = 'domain_id';
    public $timestamps = false;
}
