<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FromOpenRate extends Model
{
    protected $primaryKey = ['from_id', 'list_profile_id', 'deploy_id'];
    protected $connection = 'reporting_data';
    protected $fillable = ['from_id', 'list_profile_id', 'deploy_id', 'opens', 'delivers'];
}
