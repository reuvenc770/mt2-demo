<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SourceUrlCount
 *
 * @property int $id
 * @property int $feed_id
 * @property string $source_url
 * @property int $count
 * @property string $capture_date
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SourceUrlCount whereCaptureDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SourceUrlCount whereCount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SourceUrlCount whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SourceUrlCount whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SourceUrlCount whereSourceUrl($value)
 * @mixin \Eloquent
 */
class SourceUrlCount extends Model
{
    protected $connection = "reporting_data";
    protected $guarded = [ '' ];

    public $timestamps = false;
}
