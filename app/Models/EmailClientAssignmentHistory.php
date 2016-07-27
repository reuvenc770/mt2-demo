<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailClientAssignmentHistory extends Model
{
    protected $connection = 'attribution';
    protected $fillable = ['email_id', 'prev_client_id', 'new_client_id', 'created_at', 'updated_at'];
    protected $primaryKey = "email_id";

    public function email () {
        return $this->hasOne( 'App\Models\Email' );
    }

    public function clients () {
        return $this->hasMany( 'App\Models\Client' );
    }

    public function assignment () {
        return $this->belongsTo( 'App\Models\EmailClientAssignment' );
    }
}
