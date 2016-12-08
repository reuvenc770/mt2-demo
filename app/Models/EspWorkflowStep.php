<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EspWorkflowStep extends Model {
    protected $guarded = [''];

    public function deploy() {
        return $this->belongsTo("App\Models\Deploys");
    }

    public function workflow() {
        return $this->belongsTo("App\Models\EspWorkflow");
    }
}
