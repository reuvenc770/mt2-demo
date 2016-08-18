<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;
use MongoDB\Driver\Query;

class Proxy extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];
    public $timestamps = false;


    public function scopeActiveFirst($query)
    {
        return $query->orderBy('status','DESC');
    }
}
