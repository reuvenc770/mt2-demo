<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailClientInstance extends Model {
    
  public function email() {
    return $this->belongsTo("App\Models\Email");
  }
}
