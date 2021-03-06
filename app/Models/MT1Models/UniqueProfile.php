<?php

namespace App\Models\MT1Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class UniqueProfile extends Model
{
    use ModelCacheControl;
    protected $connection = 'mt1_data';
    protected $table = 'UniqueProfile';
    protected $primaryKey = 'profile_id';

    public $timestamps = false;
}
