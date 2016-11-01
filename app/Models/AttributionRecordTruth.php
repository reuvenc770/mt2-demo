<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributionRecordTruth extends Model
{
    const EXPIRE_COL = "recent_import";
    const ACTIVE_COL = "has_action";
    protected $connection = 'attribution';
    protected $guarded = [''];

    public function email() {
        return $this->belongsTo( 'App\Models\Email' );
    }

}
