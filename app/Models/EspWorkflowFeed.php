<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspWorkflowFeed extends Model {
    protected $guarded = [''];

    public function espWorkflow() {
        return $this->belongsTo('App\Models\EspWorkflow');
    }

    public function feed() {
        return $this->belongsTo('App\Models\Feed');
    }
}
