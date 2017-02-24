<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\FeedType
 *
 * @property int $id
 * @property string $name
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\FeedType whereName($value)
 * @mixin \Eloquent
 */
class FeedType extends Model implements IReport
{
    protected $guarded = ['id'];
    public $timestamps = false;
}