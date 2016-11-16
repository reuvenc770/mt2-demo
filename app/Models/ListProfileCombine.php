<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class ListProfileCombine extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];
    protected $connection = 'list_profile';

    public function listProfiles()
    {
        return $this->belongsToMany('App\Models\ListProfile');
    }
}