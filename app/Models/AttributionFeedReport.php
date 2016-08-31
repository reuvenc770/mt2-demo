<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
