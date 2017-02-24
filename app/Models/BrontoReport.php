<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 6/9/16
 * Time: 11:24 AM
 */

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Interfaces\IReportMapper;

/**
 * App\Models\BrontoReport
 *
 * @property int $id
 * @property int $esp_account_id
 * @property string $message_name
 * @property string $internal_id
 * @property string $start
 * @property string $message_id
 * @property string $status
 * @property string $type
 * @property string $from_email
 * @property string $from_name
 * @property bool $authentication
 * @property bool $reply_tracking
 * @property int $throttle
 * @property string $fatigue_override
 * @property int $num_sends
 * @property int $num_deliveries
 * @property int $num_hard_bad_email
 * @property int $num_hard_dest_unreach
 * @property int $num_hard_message_content
 * @property int $num_hard_bounces
 * @property int $num_soft_bad_email
 * @property int $num_soft_dest_unreach
 * @property int $num_soft_message_content
 * @property int $num_other_bounces
 * @property int $num_soft_bounces
 * @property int $num_bounces
 * @property int $uniq_opens
 * @property int $num_opens
 * @property float $avg_opens
 * @property int $uniq_clicks
 * @property int $num_clicks
 * @property float $avg_clicks
 * @property int $uniq_conversions
 * @property int $num_conversions
 * @property float $avg_conversions
 * @property int $revenue
 * @property int $num_survey_responses
 * @property int $num_friend_forwards
 * @property int $num_contact_updates
 * @property int $num_unsubscribes_by_prefs
 * @property int $num_unsubscribes_by_complaint
 * @property int $num_contact_loss
 * @property int $num_contact_loss_bounces
 * @property float $delivery_rate
 * @property float $open_rate
 * @property float $click_rate
 * @property float $click_through_rate
 * @property float $conversion_rate
 * @property float $bounce_rate
 * @property float $complaint_rate
 * @property float $contact_loss_rate
 * @property int $num_social_shares
 * @property int $num_shares_facebook
 * @property int $num_shares_twitter
 * @property int $num_shares_linked_in
 * @property int $num_shares_digg
 * @property int $num_shares_my_space
 * @property int $num_views_facebook
 * @property int $num_views_twitter
 * @property int $num_views_linked_in
 * @property int $num_views_digg
 * @property int $num_views_my_space
 * @property int $num_social_views
 * @property string $reply_email
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereAuthentication($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereAvgClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereAvgConversions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereAvgOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereBounceRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereClickRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereClickThroughRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereComplaintRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereContactLossRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereConversionRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereDeliveryRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereFatigueOverride($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereFromEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereFromName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereMessageId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereMessageName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumContactLoss($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumContactLossBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumContactUpdates($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumConversions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumDeliveries($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumFriendForwards($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumHardBadEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumHardBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumHardDestUnreach($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumHardMessageContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumOtherBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSends($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSharesDigg($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSharesFacebook($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSharesLinkedIn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSharesMySpace($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSharesTwitter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSocialShares($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSocialViews($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSoftBadEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSoftBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSoftDestUnreach($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSoftMessageContent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumSurveyResponses($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumUnsubscribesByComplaint($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumUnsubscribesByPrefs($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumViewsDigg($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumViewsFacebook($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumViewsLinkedIn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumViewsMySpace($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereNumViewsTwitter($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereOpenRate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereReplyEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereReplyTracking($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereRevenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereStart($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereThrottle($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereUniqClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereUniqConversions($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereUniqOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\BrontoReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BrontoReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "start";
    }

    public function getSubjectFieldName(){
        return "message_name";
    }
}
