<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deploy extends Model
{
    protected $guarded = ['id'];

    public function ListProfiles(){
        return $this->belongsToMany('App\Models\MT1Models\ListProfile',"list_profile","profile_id");
    }
}
