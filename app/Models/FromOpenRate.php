<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FromOpenRate extends Model
{
    protected $connection = 'mt2_reports';
    protected $fillable = ['from_id', 'list_profile_id', 'deploy_id', 'opens', 'delivers'];
}
