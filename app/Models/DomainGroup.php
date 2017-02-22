<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\DomainGroup
 *
 * @property int $id
 * @property string $name
 * @property bool $priority
 * @property string $status
 * @property string $country
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\EmailDomain[] $domains
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Email[] $emails
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DomainGroup whereCountry($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DomainGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DomainGroup whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DomainGroup wherePriority($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\DomainGroup whereStatus($value)
 * @mixin \Eloquent
 */
class DomainGroup extends Model {

  use ModelCacheControl;
  protected $guarded = ['id'];
  public $timestamps = false;


  public function domains() {
    return $this->hasMany('App\Models\EmailDomain');
  }

  public function emails() {
    return $this->hasManyThrough('App\Models\Email', 'App\Models\EmailDomain');
  }
}
