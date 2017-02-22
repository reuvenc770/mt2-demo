<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Link
 *
 * @property int $id
 * @property string $url
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Link whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Link whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Link whereUrl($value)
 * @mixin \Eloquent
 */
class Link extends Model {
    
    protected $guarded = [];
}
