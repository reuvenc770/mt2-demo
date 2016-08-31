<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributionFeedReport extends Model
{
    const BASE_TABLE_NAME = 'attribution_feed_report_';

    protected $guarded = ['id'];

    protected $connection = 'attribution';
}
