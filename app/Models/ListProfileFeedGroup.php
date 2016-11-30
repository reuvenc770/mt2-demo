<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileFeedGroup extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function feedGroup() {
        return $this->belongsTo('App\Models\FeedGroup');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }

}