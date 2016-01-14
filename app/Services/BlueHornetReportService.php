<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/13/16
 * Time: 3:31 PM
 */

namespace App\Services;

use App\Services\API\BlueHornet;
use App\Repositories\ReportsRepo;
use App\Services\Interfaces\IReportService;
use Illuminate\Support\Facades\Log;
//TODO FAILED MONITORING
//TODO Create Save Record method
/**
 * Class BlueHornetReportService
 * @package App\Services
 */
class BlueHornetReportService extends BlueHornet implements IReportService
{
    /**
     * @var ReportsRepo
     */
    protected $reportRepo;
    /**
     * @var
     */
    protected $accountNumber;
    /**
     * @var string
     */
    protected $apiKey;
    /**
     * @var string
     */
    protected $sharedSecret;

    /**
     * BlueHornetReportService constructor.
     * @param ReportsRepo $reportRepo
     * @param $accountNumber
     */
    public function __construct(ReportsRepo $reportRepo, $accountNumber)
    {
        parent::__construct();
        $this->reportRepo = $reportRepo;
    }

    /**
     * @param $date
     * @return \SimpleXMLElement
     * @throws \Exception
     */
    public function retrieveReportStats($date)
    {
        $methodData = array(
            "date" => $date
        );

        $xml = $this->buildRequest('legacy.message_stats', $methodData);
        $response = $this->sendAPIRequest($xml);
        $xmlBody = simplexml_load_string($response->getBody()->__toString());

        if ($xmlBody->item->responseCode != 201) {
            throw new \Exception("shit didnt work");
        }
        return $xmlBody;

    }

    public function insertRawStats($xmlData)
    {
        $reports = $xmlData->item->responseData->message_data;
        foreach ($reports->message as $report) {

            $convertedReport = array(
                "message" => $report['id'],
                "message_subject" => $report->message_subject,
                "message_name" => $report->message_name,
                "date_sent" => $report->date_sent,
                "message_notes" => $report->message_notes,
                "withheld_total" => $report->withheld_total,
                "globally_suppressed" => $report->globally_suppressed,
                "suppressed_total" => $report->suppressed_total,
                "bill_codes" => $report->bill_codes,
                "sent_total" => $report->sent_total,
                "sent_total_html" => $report->sent_total_html,
                "sent_total_plain" => $report->sent_total_plain,
                "sent_rate_total" => $report->sent_rate_total,
                "sent_rate_html" => $report->sent_rate_html,
                "sent_rate_plain" => $report->sent_rate_plain,
                "delivered_total" => $report->delivered_total,
                "delivered_html" => $report->delivered_html,
                "delivered_plain" => $report->delivered_plain,
                "delivered_rate_total" => $report->delivered_rate_total,
                "delivered_rate_html" => $report->delivered_rate_html,
                "delivered_rate_plain" => $report->delivered_rate_plain,
                "bounced_total" => $report->bounced_total,
                "bounced_html" => $report->bounced_html,
                "bounced_plain" => $report->bounced_plain,
                "bounced_rate_total" => $report->bounced_rate_total,
                "bounced_rate_html" => $report->bounced_rate_html,
                "bounced_rate_plain" => $report->bounced_rate_plain,
                "invalid_total" => $report->invalid_total,
                "invalid_rate_total" => $report->invalid_rate_total,
                "has_dynamic_content" => $report->has_dynamic_content,
                "has_delivery_report" => $report->has_delivery_report,
                "link_append_statement" => $report->link_append_statement,
                "timezone" => $report->timezone,
                "ftf_forwarded" => $report->ftf_forwarded,
                "ftf_signups" => $report->ftf_signups,
                "ftf_conversion_rate" => $report->ftf_conversion_rate,
                "optout_total" => $report->optout_total,
                "optout_rate_total" => $report->optout_rate_total,
                "opened_total" => $report->opened_total,
                "opened_unique" => $report->opened_unique,
                "opened_rate_unique" => $report->opened_rate_unique,
                "opened_rate_aps" => $report->opened_rate_aps,
                "clicked_total" => $report->clicked_total,
                "clicked_unique" => $report->clicked_unique,
                "clicked_rate_unique" => $report->clicked_rate_unique,
                "clicked_rate_aps" => $report->clicked_rate_aps,
                "campaign_name" => $report->campaign_name,
                "campaign_id" => $report->campaign_id
            );
            $this->reportRepo->insertRawStats($convertedReport);
        }

    }


}
