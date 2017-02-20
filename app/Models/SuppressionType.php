<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SuppressionType
 *
 * @property int $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionType whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionType whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SuppressionType extends Model
{
    protected $guarded = [ '' ];
    protected $connection = 'suppression';
}
