<?php

namespace App\Models;

use App\Models\Interfaces\IReport;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Country
 *
 * @property int $id
 * @property string $name
 * @property string $abbr
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Country whereAbbr($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Country whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Country whereName($value)
 * @mixin \Eloquent
 */
class Country extends Model implements IReport
{
    protected $guarded = ['id'];
    public $timestamps = false;
}