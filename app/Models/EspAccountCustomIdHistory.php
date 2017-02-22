<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EspAccountCustomIdHistory
 *
 * @property int $id
 * @property int $esp_account_id
 * @property int $custom_id
 * @property string $created_at
 * @property-read \App\Models\EspAccount $espAccount
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccountCustomIdHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccountCustomIdHistory whereCustomId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccountCustomIdHistory whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccountCustomIdHistory whereId($value)
 * @mixin \Eloquent
 */
class EspAccountCustomIdHistory extends Model {

  protected $table = 'esp_account_custom_id_history';
  public $timestamps = false;
  protected $guarded = ['id'];

  public function espAccount() {
    return $this->belongsTo('App\Models\EspAccount');
  }
}
