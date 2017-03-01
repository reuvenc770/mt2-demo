<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Mailable;

/**
 * App\Models\From
 *
 * @property int $id
 * @property string $from_line
 * @property bool $is_approved
 * @property string $status
 * @property bool $is_original
 * @property string $date_approved
 * @property string $approved_by
 * @property string $inactive_date
 * @property bool $internal_approved_flag
 * @property string $internal_date_approved
 * @property string $internal_approved_by
 * @property bool $copywriter
 * @property string $copywriter_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Deploy[] $deploys
 * @property-read \App\Models\FromOpenRate $openRate
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereApprovedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereCopywriter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereCopywriterName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereDateApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereFromLine($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereInactiveDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereInternalApprovedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereInternalApprovedFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereInternalDateApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereIsApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereIsOriginal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\From whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class From extends Model
{
    use Mailable;

    protected $guarded = [];

    public function deploys() {
        return $this->hasMany('App\Models\Deploy');
    }

    public function openRate() {
        return $this->hasOne('App\Models\FromOpenRate');
    }
}