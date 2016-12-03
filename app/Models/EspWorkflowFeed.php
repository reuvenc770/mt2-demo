<?php

namespace App\Models;

class EspWorkflowFeed {
    protected $guarded = [''];

    public function workflow() {
        return $this->belongsTo("App\Models\Workflow");
    }

    public function feed() {
        return $this->belongsTo("App\Models\Feed");
    }
}