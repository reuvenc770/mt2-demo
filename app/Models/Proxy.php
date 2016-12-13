<?php

namespace App\Models;

use App\Models\ModelTraits\Deletable;
use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;


class Proxy extends Model
{
    use ModelCacheControl;
    use Deletable;
    protected $guarded = ['id'];
    public $timestamps = false;


    public function scopeActiveFirst($query)
    {
        return $query->orderBy('status','DESC');
    }
}
