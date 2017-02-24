<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SuppressionList
 *
 * @property int $id
 * @property int $suppression_list_type
 * @property string $name
 * @property string $status
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SuppressionListSuppression[] $suppressions
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionList whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionList whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionList whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionList whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionList whereSuppressionListType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionList whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SuppressionList extends Model {

    protected $connection = 'suppression';
    protected $guarded = [''];

    public function suppressions() {
        return $this->hasMany('App\Models\SuppressionListSuppression');
    }
}
