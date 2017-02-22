<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AttributionLevel
 *
 * @property int $feed_id
 * @property int $level
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Feed $feed
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionLevel whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionLevel whereFeedId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionLevel whereLevel($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\AttributionLevel whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class AttributionLevel extends Model
{
    const LIVE_TABLE_NAME = 'attribution_levels';
    const BASE_TABLE_NAME = 'attribution_levels_model_';

    protected $connection = "attribution";

    public function __construct ( $tableName = null , array $attributes = [] ) {
        parent::__construct( $attributes );

        if ( !is_null( $tableName ) ) {
            $this->table = $tableName;
        }
    }

    public function feed () {
        return $this->belongsTo( 'App\Models\Feed' );
    }
}
