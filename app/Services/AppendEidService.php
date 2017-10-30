<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 10/21/16
 * Time: 3:08 PM
 */

namespace App\Services;

use App\Repositories\SuppressionGlobalOrangeRepo;
use App\Repositories\EmailRepo;
use App\Repositories\FeedRepo;
use App\Repositories\EmailAttributableFeedLatestDataRepo;
use Log;
use League\Csv\Writer;
use League\Csv\Reader;
class AppendEidService
{
    private $emailRepo;
    private $feedRepo;
    private $recordData;
    private $suppressionRepo;

    public function __construct(EmailRepo $emailRepo, FeedRepo $feedRepo, EmailAttributableFeedLatestDataRepo $recordDataRepo, SuppressionGlobalOrangeRepo $suppressionRepo)
    {
        $this->emailRepo = $emailRepo;
        $this->feedRepo = $feedRepo;
        $this->recordData = $recordDataRepo;
        $this->suppressionRepo = $suppressionRepo;
    }

    public function createFile($inputPath, $outputPath, \StdClass $appendOptions)
    {

        $f = fopen($file, 'r+');
        $reader = Reader::createFromStream($f);
        $feedName = null;
        $fieldData = array();

        $output = Writer::createFromStream(fopen($outputPath, 'a'));

        // The three property available in $appendOptions
        $header = $this->returnCsvHeader($appendOptions->includeFeed, $appendOptions->includeFields, $appendOptions->includeSuppression);

        $this->appendRow($output, $header);


        foreach ($reader as $id => $row) {
            $email = $row[0];
            $rowResult = array();
            $rowIsActive = !($this->suppressionRepo->isSuppressed($email));

            if($rowIsActive || $appendOptions->includeSuppression) {
                $emailReturn = $this->emailRepo->getEmailId($email);
                $emailExists = count($emailReturn);
                if ($emailExists) {
                    $emailId = $emailReturn[0]->id;
                    $rowResult = ["email" => $email, 'email_id' => $emailId];

                    if ($appendOptions->includeFeed) {
                        $feedId = $this->emailRepo->getCurrentAttributedFeedId($emailId);
                        $feedName = $feedId ? $this->feedRepo->fetch($feedId)->name : "##NOFEEDID##";
                        $rowResult["feedname"] = $feedName;
                    }

                    if ($appendOptions->includeFields) {
                        $fieldData = $this->recordData->getRecordDataFromEid($emailId);
                        $fieldData = $fieldData ? $fieldData->toArray() : array_fill(0, 21, '');
                        $rowResult = array_merge($rowResult, $fieldData);
                    }

                    if ($appendOptions->includeSuppression) {
                        $value = $rowIsActive ? "A" : "U";
                        $rowResult = array_merge($rowResult, ['status' => "$value"]);
                    }
                    
                    if ($rowIsActive || $appendOptions->includeSuppression) {
                        $this->appendRow($output, $rowResult);
                    }
                }
            }
        }

        // This returns the pointer
        return $output;
    }

    private function appendRow($file, array $row) {
        fwrite($file, implode(',', $row) . PHP_EOL);
    }

    private function returnCsvHeader($includeFeed, $includeFields, $includeSuppression)
    {
        $header = array("email_address","eid");
        if($includeFeed){
            $header = array_merge($header, ['feed_name']);
        }
        if($includeFields){
            $header = array_merge($header, ["first_name", "last_name", "address", "address2", "city", "state", "zip", "country", "gender", "ip", "phone",
                "source_url", "dob", "device_type", "device_name", "carrier", "capture_date", "subscribe_date", "last_action_offer_id", "last_action_date", 
                "other_fields"]);
        }
        if($includeSuppression){
            $header = array_merge($header,['status']);  //keeping style
        }

        return $header;
    }
}
