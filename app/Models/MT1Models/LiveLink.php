<?php

namespace App\Models\MT1Models;
use Illuminate\Database\Eloquent\Model;

class LiveLink extends Model
{
    protected $connection = 'legacy_data_sync';
    protected $table = 'links';
    protected $primaryKey = 'link_id';
    public $timestamps = false;
}