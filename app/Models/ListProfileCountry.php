<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListProfileCountry extends Model
{
    protected $guarded = [ '' ];
    public $timestamps = false;
    protected $connection = 'list_profile';

    public function country () {
        return $this->belongsTo( 'App\Models\Country' );
    }

    public function listProfile () {
        return $this->belongsTo( 'App\Models\ListProfile' );
    }
}
