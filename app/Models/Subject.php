<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Mailable;

/**
 * App\Models\Subject
 *
 * @property int $id
 * @property string $subject_line
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
 * @property-read \App\Models\SubjectOpenRate $openRate
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereApprovedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereCopywriter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereCopywriterName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereDateApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereInactiveDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereInternalApprovedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereInternalApprovedFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereInternalDateApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereIsApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereIsOriginal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereSubjectLine($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Subject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Subject extends Model
{
    use Mailable;

    protected $guarded = [];

    public function deploys() {
        return $this->hasMany('App\Models\Deploy');
    }

    public function openRate() {
        return $this->hasOne('App\Models\SubjectOpenRate');
    }
}
