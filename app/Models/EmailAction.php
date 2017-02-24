<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailAction
 *
 * @property int $id
 * @property int $email_id
 * @property int $deploy_id
 * @property int $esp_account_id
 * @property int $esp_internal_id
 * @property bool $action_id
 * @property string $datetime
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\ActionType $actionType
 * @property-read \App\Models\Email $email
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereActionId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereDatetime($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereEspInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailAction whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EmailAction extends Model {
  
  protected $guarded = ['id'];
  protected $connection = "reporting_data";

  public function email() {
    return $this->belongsTo('App\Models\Email');
  }

  public function actionType() {
    return $this->belongsTo('App\Models\ActionType');
  }
}