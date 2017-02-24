<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\CakeEncryptedLink
 *
 * @property int $id
 * @property int $affiliate_id
 * @property int $creative_id
 * @property string $encrypted_hash
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeEncryptedLink whereAffiliateId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeEncryptedLink whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeEncryptedLink whereCreativeId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeEncryptedLink whereEncryptedHash($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeEncryptedLink whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\CakeEncryptedLink whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CakeEncryptedLink extends Model {

    protected $guarded = ['id'];
    
}
