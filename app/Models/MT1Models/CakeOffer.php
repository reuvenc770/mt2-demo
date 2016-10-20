<?php

namespace App\Models\MT1Models;

use Illuminate\Database\Eloquent\Model;

class CakeOffer extends Model {
    protected $connection = 'mt1_data';
    protected $table = 'CakeOffer';
    protected $primaryKey = 'offerID';

}