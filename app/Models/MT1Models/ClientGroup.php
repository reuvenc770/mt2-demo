<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class ClientGroup extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'ClientGroup';
    protected $primaryKey = 'client_group_id';

}
