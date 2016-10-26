<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 10/21/16
 * Time: 3:08 PM
 */

namespace App\Services;

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

    public function createFile($file, $feed, $fields)
    {
        $reader = Reader::createFromPath($file);

        $rows = $reader->fetchAssoc(["email"]);
        $csvData = array();
        $feedName = null;
        $fieldData = null;
        $i = 1;
        try {
            foreach ($rows as $row) {
                print_r($i);
                $emailReturn = $this->emailRepo->getEmailId($row['email']);
                $emailId = $emailReturn[0]->id;
                if ($feed) {
                    $feedId = $this->emailRepo->getCurrentAttributedFeedId($emailId);
                    $feedName = $this->feedRepo->getFeed($feedId)->name;
                }
                if ($fields) {
                    $fieldData = $this->recordData->getRecordDataFromEid($emailId);
                    $fieldData =  $fieldData ?  $fieldData->toArray() : array();
                }
                $csvData[] = array_merge(["email" => $row['email'],"email_id" => $emailId, "feedname" => $feedName], $fieldData);
                $i++;
            }
        } catch (\Exception $e) {
            Log::info($e);
        }

        return $this->createCsv($csvData,$feed,$fields);
    }

    public function createCsv($data,$feed,$fields)
    {
        $newPath = storage_path('downloads') . "/file.csv";
        $writer = Writer::createFromFileObject(new \SplTempFileObject());
        $schema = $this->returnCsvHeader($feed,$fields);

        $writer->insertOne($schema);

        foreach ($data as $row) {
            $writer->insertOne($row);
        }
        return $writer->__toString();
    }


    private function returnCsvHeader($feed,$fields)
    {
        $header = array();
        $base = array("email_adddress","eid");
        if($feed){
            $header = array_merge($base, ['feed_name']);
        }
        if($fields){
            $header = array_merge($header, ["first_name", "last_name", "address", "address2", "city", "state", "zip", "country", "gender", "ip", "phone",
                "source_url", "dob", "device_type", "device_name", "carrier", "capture_date", "other_fields", "created_at",
                "updated_at"]);
        }
        return $header;
    }
}
