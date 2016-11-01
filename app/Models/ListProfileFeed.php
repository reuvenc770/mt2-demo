<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileFeed extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function feed() {
        return $this->belongsTo('App\Models\Feed');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }

}