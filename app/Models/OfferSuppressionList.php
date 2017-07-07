<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferSuppressionList extends Model {
    protected $primaryKey = ['offer_id', 'suppression_list_id'];
    protected $guarded = [];
    public $timestamps = false;
    protected $connection = 'suppression';
}
