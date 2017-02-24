<?php

namespace App\Models;

use App\Models\Interfaces\IReportMapper;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MaroReport
 *
 * @property int $id
 * @property int $esp_account_id
 * @property int $internal_id
 * @property string $name
 * @property string $status
 * @property int $sent
 * @property int $delivered
 * @property int $open
 * @property int $click
 * @property int $bounce
 * @property string $send_at
 * @property string $sent_at
 * @property string $maro_created_at
 * @property string $maro_updated_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $from_name
 * @property string $from_email
 * @property string $subject
 * @property int $unique_opens
 * @property int $unique_clicks
 * @property int $unsubscribes
 * @property int $complaints
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereBounce($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereClick($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereComplaints($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereDelivered($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereEspAccountId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereFromEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereFromName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereInternalId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereMaroCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereMaroUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereOpen($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereSendAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereSent($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereSentAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereStatus($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereSubject($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereUniqueClicks($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereUniqueOpens($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereUnsubscribes($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Models\MaroReport whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MaroReport extends Model implements IReportMapper
{
    protected $guarded = ['id'];
    protected $connection = "reporting_data";

    public function getDateFieldName(){
        return "sent_at";
    }

    public function getSubjectFieldName(){
        return "subject";
    }
}
