<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class DoingBusinessAs extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];

    public function scopeActiveFirst($query)
    {
        return $query->orderBy('status','DESC');
    }
}
