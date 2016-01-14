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
        $xmlBody = simplexml_load_string($response->getBody());

        if ($xmlBody->item->responseCode != 201) {
            throw new \Exception("shit didnt work");
        }
        //Insert into raw bh table
        //Facade insert into clean table.
        return $xmlBody;

    }


}