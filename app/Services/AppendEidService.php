<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 10/21/16
 * Time: 3:08 PM
 */

namespace App\Services;

use App\Facades\Suppression;
use App\Repositories\EmailRepo;
use App\Repositories\FeedRepo;
use App\Repositories\RecordDataRepo;
use Log;
use League\Csv\Writer;
use League\Csv\Reader;
class AppendEidService
{
    private $emailRepo;
    private $feedRepo;
    private $recordData;

    public function __construct(EmailRepo $emailRepo, FeedRepo $feedRepo, RecordDataRepo $recordDataRepo)
    {
        $this->emailRepo = $emailRepo;
        $this->feedRepo = $feedRepo;
        $this->recordData = $recordDataRepo;
    }

    public function createFile($file, $includeFeed, $includeFields, $includeSuppression)
    {
        $reader = Reader::createFromPath($file);

        $rows = $reader->fetchAssoc(["email"]);
        $csvData = array();
        $feedName = null;
        $fieldData = array();
        $stats = null;
        try {
            foreach ($rows as $row) {

                $suppressionInfo = Suppression::checkGlobalSuppression($row['email']);
                $rowIsActive = count($suppressionInfo) == 0;
                if($rowIsActive || $includeSuppression) {
                    $emailReturn = $this->emailRepo->getEmailId($row['email']);
                    $emailExists = count($emailReturn);
                    if ($emailExists) {
                        $emailId = $emailReturn[0]->id;
                        if ($includeFeed) {
                            $feedId = $this->emailRepo->getCurrentAttributedFeedId($emailId);
                            $feedName = $feedId ? $this->feedRepo->fetch($feedId)->name : "##NOFEEDID##";
                        }
                        if ($includeFields) {
                            $fieldData = $this->recordData->getRecordDataFromEid($emailId);
                            $fieldData = $fieldData ? $fieldData->toArray() : array_fill(0, 19, '');
                        }
                        $rowResult = array_merge(["email" => $row['email'], "email_id" => $emailId, "feedname" => $feedName,], $fieldData);
                        if ($includeSuppression) {
                            $value = $rowIsActive ? "A" : "U";
                            $rowResult = array_merge($rowResult, ['status' => "$value"]);
                        }
                        $csvData[] = $rowResult;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::info($e);
        }

        return $this->createCsv($csvData,$includeFeed,$includeFields,$includeSuppression);
    }

    public function createCsv($data,$includeFeed,$includeFields,$includeSuppression)
    {

        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $schema = $this->returnCsvHeader($includeFeed,$includeFields,$includeSuppression);

        $writer->insertOne($schema);

        foreach ($data as $row) {
            unset($row['is_deliverable']);
            $writer->insertOne($row);
        }
        return $writer->__toString();
    }


    private function returnCsvHeader($includeFeed,$includeFields, $includeSuppression)
    {
        $header = array("email_address","eid");
        if($includeFeed){
            $header = array_merge($header, ['feed_name']);
        }
        if($includeFields){
            $header = array_merge($header, ["first_name", "last_name", "address", "address2", "city", "state", "zip", "country", "gender", "ip", "phone",
                "source_url", "dob", "device_type", "device_name", "carrier", "capture_date", "subscribe_date", "last_action_offer_id", "last_action_date", 
                "other_fields", "created_at", "updated_at"]);
        }
        if($includeSuppression){
            $header = array_merge($header,['status']);  //keeping style
        }

        return $header;
    }
}
