<?php

namespace App\Models\MT1Models;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $connection = 'mt1_data';
    protected $table = 'links';
    protected $primaryKey = 'link_id';
}