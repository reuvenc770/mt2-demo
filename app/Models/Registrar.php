<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Deletable;
class Registrar extends Model
{
    use ModelCacheControl;
    use Deletable;
    protected $guarded = ['id'];
    public $timestamps = false;


    public function domains(){
        return $this->hasMany('App\Models\Domain');
    }
    public function scopeActiveFirst($query)
    {
        return $query->orderBy('status','DESC');
    }
}
