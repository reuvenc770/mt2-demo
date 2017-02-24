<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributionFeedReport
 *
 * @property int $id
 * @property int $feed_id
 * @property float $revenue
 * @property int $mt2_uniques
 * @property int $mt1_uniques
 * @property string $date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionFeedReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionFeedReport whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionFeedReport whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionFeedReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionFeedReport whereMt1Uniques($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionFeedReport whereMt2Uniques($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionFeedReport whereRevenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionFeedReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributionFeedReport extends Model
{
    const LIVE_TABLE_NAME = 'attribution_feed_reports';
    const BASE_TABLE_NAME = 'attribution_feed_report_';

    protected $guarded = ['id'];

    protected $connection = 'attribution';

    public function switchToLiveTable () {
        $this->table = self::LIVE_TABLE_NAME;
    }

    public function setModelId ( $modelId ) {
        $this->table = self::BASE_TABLE_NAME . $modelId;
    }
}
