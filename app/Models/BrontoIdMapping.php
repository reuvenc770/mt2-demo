<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BrontoIdMapping
 *
 * @property string $primary_id
 * @property int $generated_id
 * @property int $esp_account_id
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoIdMapping whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoIdMapping whereGeneratedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoIdMapping wherePrimaryId($value)
 * @mixin \Eloquent
 */
class BrontoIdMapping extends Model
{
    protected $guarded = [''];
    protected $connection = "reporting_data";
    public $timestamps = false;
}
