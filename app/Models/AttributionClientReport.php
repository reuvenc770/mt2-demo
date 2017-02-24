<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributionClientReport
 *
 * @property int $id
 * @property int $client_stats_grouping_id
 * @property float $standard_revenue
 * @property float $cpm_revenue
 * @property int $mt1_uniques
 * @property int $mt2_uniques
 * @property string $date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereClientStatsGroupingId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereCpmRevenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereMt1Uniques($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereMt2Uniques($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereStandardRevenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionClientReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributionClientReport extends Model
{
    const LIVE_TABLE_NAME = 'attribution_client_reports';
    const BASE_TABLE_NAME = 'attribution_client_report_';

    protected $guarded = ['id'];

    protected $connection = 'attribution';

    public function switchToLiveTable () {
        $this->table = self::LIVE_TABLE_NAME;
    }

    public function setModelId ( $modelId ) {
        $this->table = self::BASE_TABLE_NAME . $modelId;
    }
}
