<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagePermission extends Model
{
    public function permissions () {
        return $this->hasMany( 'App\Models\Permission' , 'id' , 'permission_id' );
    }
}
