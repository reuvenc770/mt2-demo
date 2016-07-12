<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributionTransientRecord extends Model
{
    const BASE_TABLE_NAME = 'attribution_transient_records_model_';

    protected $connection = 'attribution';

    public function __construct ( $tableName = null , array $attributes = [] ) {
        parent::__construct( $attributes );

        if ( !is_null( $tableName ) ) {
            $this->table = $tableName;
        }
    }
}
