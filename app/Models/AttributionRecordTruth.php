<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributionRecordTruth
 *
 * @property int $email_id
 * @property bool $recent_import
 * @property bool $has_action
 * @property bool $action_expired
 * @property bool $additional_imports
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Email $email
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordTruth whereActionExpired($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordTruth whereAdditionalImports($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordTruth whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordTruth whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordTruth whereHasAction($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordTruth whereRecentImport($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordTruth whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributionRecordTruth extends Model
{
    const EXPIRE_COL = "recent_import";
    const ACTIVE_COL = "has_action";
    protected $connection = 'attribution';
    protected $guarded = [''];

    public function email() {
        return $this->belongsTo( 'App\Models\Email' );
    }

}
