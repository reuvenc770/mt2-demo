<?php

namespace App\Models\Cake;

use Illuminate\Database\Eloquent\Model;

class MassAdjustment extends Model
{
    protected $connection = 'reporting_data';
    protected $guarded = ['id'];
    protected $primaryKey = 'deploy_id';
}
