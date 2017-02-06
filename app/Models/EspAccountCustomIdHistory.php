<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

class EspAccountCustomIdHistory extends Model {

  protected $table = 'esp_account_custom_id_history';
  public $timestamps = false;
  protected $guarded = ['id'];

  public function espAccount() {
    return $this->belongsTo('App\Models\EspAccount');
  }
}
