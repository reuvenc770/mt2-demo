<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EtlPickup
 *
 * @property int $name
 * @property int $stop_point
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EtlPickup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EtlPickup whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EtlPickup whereStopPoint($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EtlPickup whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EtlPickup extends Model {
    protected $guarded = [];
    protected $primaryKey = 'name';
}
