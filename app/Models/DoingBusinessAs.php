<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class DoingBusinessAs extends Model
{
    use ModelCacheControl;
    protected $guarded = ['id'];
    public $timestamps = false;
}
