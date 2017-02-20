<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SuppressionListSuppression
 *
 * @property int $id
 * @property int $suppression_list_id
 * @property string $email_address
 * @property string $lower_case_md5
 * @property string $upper_case_md5
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\SuppressionList $list
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListSuppression whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListSuppression whereEmailAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListSuppression whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListSuppression whereLowerCaseMd5($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListSuppression whereSuppressionListId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListSuppression whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\SuppressionListSuppression whereUpperCaseMd5($value)
 * @mixin \Eloquent
 */
class SuppressionListSuppression extends Model {

    protected $connection = 'suppression';
    protected $guarded = [''];
    
    public function list() {
        return $this->belongsTo('App\Models\SuppressionList');
    }
}
