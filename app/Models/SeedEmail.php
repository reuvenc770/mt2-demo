<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SeedEmail
 *
 * @property int $id
 * @property string $email_address
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SeedEmail whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SeedEmail whereId($value)
 * @mixin \Eloquent
 */
class SeedEmail extends Model
{
    protected  $guarded= ['id'];
    public $timestamps = false; 
}
