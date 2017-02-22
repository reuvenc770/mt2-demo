<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Permission
 *
 * @property int $id
 * @property string $name
 * @property string $crud_type
 * @property int $rank
 * @property int $parent
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereCrudType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereParent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Permission whereRank($value)
 * @mixin \Eloquent
 */
class Permission extends Model
{
    CONST TYPE_CREATE = 'create';
    CONST TYPE_READ = 'read';
    CONST TYPE_UPDATE = 'update';
    CONST TYPE_DELETE = 'delete';
    protected $guarded = ['id'];
    public $timestamps = false;
}
