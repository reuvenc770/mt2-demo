<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectOpenRate extends Model
{
    protected $primaryKey = ['subject_id', 'list_profile_combine_id', 'deploy_id'];
    protected $connection = 'reporting_data';
    protected $fillable = ['subject_id', 'list_profile_combine_id', 'deploy_id', 'opens', 'delivers'];

    public function subject() {
        return $this->belongsTo('App\Models\Subject');
    }
}
