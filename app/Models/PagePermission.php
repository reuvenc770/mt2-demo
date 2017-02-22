<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PagePermission
 *
 * @property int $id
 * @property int $page_id
 * @property int $permission_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PagePermission whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PagePermission whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PagePermission wherePageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PagePermission wherePermissionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PagePermission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PagePermission extends Model
{
    public function permissions () {
        return $this->hasMany( 'App\Models\Permission' , 'id' , 'permission_id' );
    }
}
