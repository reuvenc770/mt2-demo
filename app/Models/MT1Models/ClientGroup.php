<?php

namespace App\Models\MT1Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class ClientGroup extends Model
{
    use ModelCacheControl;
    protected $connection = 'mt1mail';
    protected $table = 'ClientGroup';
    protected $primaryKey = 'client_group_id';

}
