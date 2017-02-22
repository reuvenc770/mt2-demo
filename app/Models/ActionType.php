<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActionType
 *
 * @property int $id
 * @property string $name
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailAction[] $emailActions
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ActionType whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\ActionType whereName($value)
 * @mixin \Eloquent
 */
class ActionType extends Model {
  
  protected $guarded = ['id'];
  protected $connection = "reporting_data";

  public function emailActions() {
    return $this->hasMany('App\Models\EmailAction');
  }
}
