<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributionLevel extends Model
{
    public function client () {
        return $this->belongsTo( 'App\Models\Client' );
    }
}
