<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileVertical extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function vertical() {
        return $this->belongsTo('App\Models\CakeVertical', 'cake_vertical_id');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }
}