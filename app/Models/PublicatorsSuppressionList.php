<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PublicatorsSuppressionList
 *
 * @property string $account_name
 * @property int $suppression_list_id
 * @property-read \App\Models\EspAccount $espAccount
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsSuppressionList whereAccountName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsSuppressionList whereSuppressionListId($value)
 * @mixin \Eloquent
 */
class PublicatorsSuppressionList extends Model {
  
    public $timestamps = false;

    public function espAccount() {
        return $this->hasOne('App\Models\EspAccount', 'account_name');
    }
}