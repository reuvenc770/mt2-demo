<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SuppressionListType
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListType whereName($value)
 * @mixin \Eloquent
 */
class SuppressionListType extends Model {
    protected $guarded = ['id'];
    public $timestamps = false;
    protected $connection = 'suppression';
}