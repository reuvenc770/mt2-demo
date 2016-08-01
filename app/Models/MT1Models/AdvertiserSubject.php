<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserSubject extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'advertiser_subject';
    protected $primaryKey = 'subject_id';

}