<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileClient extends Model
{
    protected $guarded = [''];
    public $timestamps = false;
    protected $connection = '';

    public function client() {
        return $this->belongsTo('App\Models\Client');
    }

    public function listProfile() {
        return $this->belongsTo('App\Models\ListProfile');
    }

}
