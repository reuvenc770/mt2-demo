<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspWorkflow extends Model {
    protected $guarded = ['id'];

    public function steps() {
        return $this->hasMany("App\Models\EspWorkflowStep");
    }

    public function feeds() {
        return $this->hasManyThrough("App\Models\Feed", "App\Models\EspWorkflowFeed");
    }
}