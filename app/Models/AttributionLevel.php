<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributionLevel extends Model
{
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
