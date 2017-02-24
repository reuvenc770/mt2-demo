<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PublicatorsReport
 *
 * @property int $id
 * @property int $esp_account_id
 * @property int $internal_id
 * @property string $CreatedDate
 * @property string $SentDate
 * @property string $CampaignStatusDescription
 * @property int $CampaignStatusType
 * @property int $ListId
 * @property string $ListName
 * @property int $CampaignEventType
 * @property string $CampaignEventTypeDescription
 * @property string $FromName
 * @property string $FromEmail
 * @property string $Subject
 * @property string $Address
 * @property string $Contact
 * @property int $TotalMailsSent
 * @property int $TotalOpened
 * @property int $TotalUniqueOpened
 * @property int $TotalOpenedFromSmartPhone
 * @property int $TotalClicks
 * @property int $TotalUniqueClicks
 * @property int $TotalBounces
 * @property int $TotalUniqueUnsubscribed
 * @property int $TotalForwards
 * @property int $TotalPurchases
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereCampaignEventType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereCampaignEventTypeDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereCampaignStatusDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereCampaignStatusType($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereContact($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereCreatedDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereFromEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereFromName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereListId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereListName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereSentDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalForwards($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalMailsSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalOpened($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalOpenedFromSmartPhone($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalPurchases($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalUniqueClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalUniqueOpened($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereTotalUniqueUnsubscribed($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\PublicatorsReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PublicatorsReport extends Model implements IReportMapper
{
    protected $guarded = [ "id" ];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "SentDate";
    }

    public function getSubjectFieldName(){
        return "Subject";
    }
}
