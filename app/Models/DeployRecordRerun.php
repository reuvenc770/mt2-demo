<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DeployRecordRerun
 *
 * @property int $deploy_id
 * @property int $esp_internal_id
 * @property int $esp_account_id
 * @property bool $delivers
 * @property bool $opens
 * @property bool $clicks
 * @property bool $unsubs
 * @property bool $complaints
 * @property bool $bounces
 * @property-read \App\Models\EspAccount $espAccount
 * @property-read \App\Models\StandardReport $report
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereComplaints($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereDelivers($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereEspInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DeployRecordRerun whereUnsubs($value)
 * @mixin \Eloquent
 */
class DeployRecordRerun extends Model {

    protected $fillable = ['deploy_id', 'esp_internal_id', 'esp_account_id', 'delivers', 'opens', 'clicks'];
    public $timestamps = false;
    protected $primaryKey = 'deploy_id';
  
    public function report() {
        return $this->belongsTo('App\Models\StandardReport');
    }

    public function espAccount() {
        return $this->belongsTo('App\Models\EspAccount');
    }
}