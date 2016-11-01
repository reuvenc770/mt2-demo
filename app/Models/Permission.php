<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    CONST TYPE_CREATE = 'create';
    CONST TYPE_READ = 'read';
    CONST TYPE_UPDATE = 'update';
    CONST TYPE_DELETE = 'delete';
    protected $guarded = ['id'];
    public $timestamps = false;
}
