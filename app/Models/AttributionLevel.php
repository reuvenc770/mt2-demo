<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributionLevel extends Model
{
    protected $connection = "attribution";

    public function __construct ( $tableName = null , array $attributes = [] ) {
        parent::__construct( $attributes );

        if ( !is_null( $tableName ) ) {
            $this->table = $tableName;
        }
    }

    public function client () {
        return $this->belongsTo( 'App\Models\Client' );
    }
}
