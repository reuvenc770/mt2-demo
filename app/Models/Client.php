<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\ModelCacheControl;

class Client extends Model
{
    use ModelCacheControl;
    protected $guarded = [];

    public function feeds() {
        return $this->hasMany('App\Models\Feeds');
    }
}
