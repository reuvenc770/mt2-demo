<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 3/8/16
 * Time: 3:57 PM
 */

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GetResponseReport
 *
 * @property int $id
 * @property int $esp_account_id
 * @property string $name
 * @property string $subject
 * @property string $internal_id
 * @property string $info_url
 * @property string $from_email
 * @property string $from_name
 * @property string $reply_name
 * @property string $reply_email
 * @property string $html
 * @property int $sent
 * @property int $total_open
 * @property int $unique_open
 * @property int $total_click
 * @property int $unique_click
 * @property int $unsubscribes
 * @property int $bounces
 * @property int $complaints
 * @property string $sent_on
 * @property string $created_on
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereBounces($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereComplaints($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereCreatedOn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereFromEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereFromName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereHtml($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereInfoUrl($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereReplyEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereReplyName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereSentOn($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereTotalClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereTotalOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereUniqueClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereUniqueOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereUnsubscribes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\GetResponseReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class GetResponseReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "sent_on";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}
