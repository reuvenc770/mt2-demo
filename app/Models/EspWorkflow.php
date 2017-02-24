<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EspWorkflow
 *
 * @property int $id
 * @property string $name
 * @property int $esp_account_id
 * @property bool $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Feed[] $feeds
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EspWorkflowStep[] $steps
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflow whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflow whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflow whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflow whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflow whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspWorkflow whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class EspWorkflow extends Model {
    protected $guarded = ['id'];

    public function steps() {
        return $this->hasMany('App\Models\EspWorkflowStep');
    }

    public function feeds() {
        return $this->belongsToMany('App\Models\Feed', 'esp_workflow_feeds');
    }
}