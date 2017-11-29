<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailOversightFeed extends Model
{
    protected $guarded = [];
    protected $primaryKey = 'feed_id';

    public function feed() {
        return $this->belongsTo( 'App\Models\Feed' );
    }
}
