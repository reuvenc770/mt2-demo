<?php
/**
 * @author Adam Chin <achin@zetainteractive.com>
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientPayout extends Model
{
    protected $connection = 'attribution';
    protected $fillable = ['client_id', 'client_payout_type_id', 'amount'];

    public function type () {
        return $this->hasOne( 'App\Models\ClientPayoutType' );
    }
}
