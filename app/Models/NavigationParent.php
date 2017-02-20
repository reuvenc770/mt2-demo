<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NavigationParent
 *
 * @property int $id
 * @property string $name
 * @property int $rank
 * @property string $glyth
 * @method static \Illuminate\Database\Query\Builder|\App\Models\NavigationParent whereGlyth($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\NavigationParent whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\NavigationParent whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\NavigationParent whereRank($value)
 * @mixin \Eloquent
 */
class NavigationParent extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;
}
