<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function espAccount(){
        $this->hasOne("App\\Models\\EspAccount");
    }

    public function esp(){
        $this->hasOne("App\\Models\\Esp");
    }

}
