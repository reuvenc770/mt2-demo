<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SubjectOpenRate extends Model
{
    protected $connection = 'mt2_reports';
    protected $fillable = ['subject_id', 'list_profile_id', 'deploy_id', 'opens', 'delivers'];
}
