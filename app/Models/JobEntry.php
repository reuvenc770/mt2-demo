<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobEntry extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;



    public function scopewhereEspAccount($query, $jobName, $espName, $accountName){
        return $query->where('job_name', $jobName)->where('account_name',$espName)->where('account_number',$accountName);
    }
}
