<?php

namespace App\Models;

use App\Models\ModelTraits\Deletable;
use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DoingBusinessAs
 *
 * @property int $id
 * @property string $dba_name
 * @property string $registrant_name
 * @property string $address
 * @property string $address_2
 * @property string $city
 * @property string $state
 * @property string $zip
 * @property string $dba_email
 * @property string $phone
 * @property string $po_boxes
 * @property string $entity_name
 * @property bool $status
 * @property string $notes
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs activeFirst()
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereAddress2($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereCity($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereDbaEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereDbaName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereEntityName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereNotes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs wherePoBoxes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereRegistrantName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereState($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DoingBusinessAs whereZip($value)
 * @mixin \Eloquent
 */
class DoingBusinessAs extends Model
{
    use ModelCacheControl;
    use Deletable;
    protected $guarded = ['id'];
    public $timestamps = false;

    public function scopeActiveFirst($query)
    {
        return $query->orderBy('status','DESC');
    }
}
