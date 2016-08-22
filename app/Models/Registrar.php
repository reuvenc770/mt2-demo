<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class Registrar extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];
    public $timestamps = false;

    public function scopeActiveFirst($query)
    {
        return $query->orderBy('status','DESC');
    }
}
