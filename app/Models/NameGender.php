<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\NameGender
 *
 * @property int $name
 * @property string $gender
 * @method static \Illuminate\Database\Query\Builder|\App\Models\NameGender whereGender($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\NameGender whereName($value)
 * @mixin \Eloquent
 */
class NameGender extends Model {
    protected $primaryKey = 'name';
}
