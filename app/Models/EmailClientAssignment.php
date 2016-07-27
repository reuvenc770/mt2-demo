<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailClientAssignment extends Model
{
    protected $connection = 'attribution';
    protected $fillable = ['email_id', 'client_id', 'capture_date'];
    protected $primaryKey = "email_id";


    public function email () {
        return $this->hasOne( 'App\Models\Email' );
    }

    public function client () {
        return $this->hasOne( 'App\Models\Client' );
    }

    public function history () {
        return $this->hasMany( 'App\Models\EmailClientAssignmentHistory' );
    }
}
