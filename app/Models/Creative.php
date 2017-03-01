<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Mailable;

/**
 * App\Models\Creative
 *
 * @property int $id
 * @property string $file_name
 * @property string $creative_html
 * @property bool $is_approved
 * @property string $status
 * @property bool $is_original
 * @property bool $trigger_flag
 * @property string $creative_date
 * @property string $inactive_date
 * @property string $unsub_image
 * @property int $default_subject
 * @property int $default_from
 * @property string $image_directory
 * @property string $thumbnail
 * @property string $date_approved
 * @property string $approved_by
 * @property bool $content_id
 * @property bool $header_id
 * @property bool $body_content_id
 * @property string $style_id
 * @property bool $replace_flag
 * @property bool $mediactivate_flag
 * @property bool $hitpath_flag
 * @property string $comm_wizard_c3
 * @property int $comm_wizard_cid
 * @property int $comm_wizard_progid
 * @property string $cr
 * @property string $landing_page
 * @property bool $is_internally_approved
 * @property string $internal_date_approved
 * @property string $internal_approved_by
 * @property bool $copywriter
 * @property string $copywriter_name
 * @property string $original_html
 * @property int $deleted_by
 * @property bool $host_images
 * @property bool $needs_processing
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\CreativeClickthroughRate $clickthroughRate
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Deploy[] $deploys
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereApprovedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereBodyContentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCommWizardC3($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCommWizardCid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCommWizardProgid($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereContentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCopywriter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCopywriterName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCr($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCreativeDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereCreativeHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereDateApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereDefaultFrom($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereDefaultSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereDeletedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereFileName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereHeaderId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereHitpathFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereHostImages($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereImageDirectory($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereInactiveDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereInternalApprovedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereInternalDateApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereIsApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereIsInternallyApproved($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereIsOriginal($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereLandingPage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereMediactivateFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereNeedsProcessing($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereOriginalHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereReplaceFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereStyleId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereThumbnail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereTriggerFlag($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereUnsubImage($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Creative whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Creative extends Model
{
    use Mailable;

    protected $guarded = [];
    const DEFAULT_DOMAIN = "wealthpurse.com";

    public function deploys() {
        return $this->hasMany('App\Models\Deploy');
    }

    public function clickthroughRate() {
        return $this->hasOne('App\Models\CreativeClickthroughRate');
    }
}
