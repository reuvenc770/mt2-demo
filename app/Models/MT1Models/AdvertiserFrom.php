<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserFrom extends Model
{
    protected $connection = 'mt1mail';
    protected $table = 'advertiser_from';
    protected $primaryKey = 'from_id';

}