<?php

namespace App\Models;

use App\Models\ModelTraits\ModelCacheControl;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EmailDomain
 *
 * @property int $id
 * @property int $domain_group_id
 * @property string $domain_name
 * @property bool $is_suppressed
 * @property-read \App\Models\DomainGroup $domainGroup
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Email[] $email
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDomain whereDomainGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDomain whereDomainName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDomain whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EmailDomain whereIsSuppressed($value)
 * @mixin \Eloquent
 */
class EmailDomain extends Model {

  use ModelCacheControl;
  protected $guarded = ['id'];
  public $timestamps = false;
  public function email() {
    return $this->hasMany('App\Models\Email');
  }

  public function domainGroup() {
    return $this->belongsTo('App\Models\DomainGroup');
  }
}
