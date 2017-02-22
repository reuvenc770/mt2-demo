<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SuppressionReason
 *
 * @property int $id
 * @property string $display_status
 * @property string $legacy_status
 * @property int $esp_id
 * @property int $suppression_type
 * @property bool $display
 * @property-read \App\Models\Esp $esp
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Suppression[] $suppressions
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionReason displayable()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionReason whereDisplay($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionReason whereDisplayStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionReason whereEspId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionReason whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionReason whereLegacyStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionReason whereSuppressionType($value)
 * @mixin \Eloquent
 */
class SuppressionReason extends Model {

    protected $guarded = ['id'];
    public $timestamps = false;

    public function suppressions() {
        return $this->hasMany('App\Models\Suppression');
    }

    public function esp()
    {
        return $this->belongsTo('App\Models\Esp');
    }

    /**
     * Scope a query to only include active reasons
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDisplayable($query)
    {
        return $query->where('display',true);
    }
}
