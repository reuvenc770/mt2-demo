<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributionRecordReport
 *
 * @property int $id
 * @property int $email_id
 * @property int $deploy_id
 * @property int $offer_id
 * @property float $revenue
 * @property string $date
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordReport whereDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordReport whereDeployId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordReport whereEmailId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordReport whereOfferId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordReport whereRevenue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionRecordReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributionRecordReport extends Model
{
    protected $guarded = ['id'];

    protected $connection = 'attribution';
}
