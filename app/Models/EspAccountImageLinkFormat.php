<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EspAccountImageLinkFormat
 *
 * @property int $esp_account_id
 * @property bool $remove_file_extension
 * @property string $url_format
 * @property-read \App\Models\EspAccount $espAccount
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccountImageLinkFormat whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccountImageLinkFormat whereRemoveFileExtension($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\EspAccountImageLinkFormat whereUrlFormat($value)
 * @mixin \Eloquent
 */
class EspAccountImageLinkFormat extends Model {
    protected $guarded = [];
    protected $primaryKey = 'esp_account_id';
    public $timestamps = false;

    public function espAccount() {
        return $this->belongsTo('App\Models\EspAccount');
    }
}
