<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeedVertical
 *
 * @property int $id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedVertical whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedVertical whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedVertical whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FeedVertical extends Model
{
    protected $guarded = [];
    public $timestamps = false;
}
