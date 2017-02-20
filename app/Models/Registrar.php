<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;
use App\Models\ModelTraits\Deletable;
/**
 * App\Models\Registrar
 *
 * @property int $id
 * @property string $name
 * @property string $username
 * @property string $last_cc
 * @property string $contact_credit_card
 * @property bool $status
 * @property string $dba_names
 * @property string $password
 * @property string $notes
 * @property string $other_last_cc
 * @property string $other_contact_credit_card
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Domain[] $domains
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar activeFirst()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereContactCreditCard($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereDbaNames($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereLastCc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereNotes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereOtherContactCreditCard($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereOtherLastCc($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\Registrar whereUsername($value)
 * @mixin \Eloquent
 */
class Registrar extends Model
{
    use ModelCacheControl;
    use Deletable;
    protected $guarded = ['id'];
    public $timestamps = false;


    public function domains(){
        return $this->hasMany('App\Models\Domain');
    }
    public function scopeActiveFirst($query)
    {
        return $query->orderBy('status','DESC');
    }
}
