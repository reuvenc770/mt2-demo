<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EspWorkflowStep
 *
 * @property int $esp_workflow_id
 * @property int $step
 * @property int $deploy_id
 * @property int $offer_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Deploy $deploy
 * @property-read \App\Models\EspWorkflow $workflow
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflowStep whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflowStep whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflowStep whereEspWorkflowId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflowStep whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflowStep whereStep($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflowStep whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EspWorkflowStep extends Model {
    protected $guarded = [''];

    public function deploy() {
        return $this->belongsTo('App\Models\Deploy');
    }

    public function workflow() {
        return $this->belongsTo('App\Models\EspWorkflow');
    }
}
