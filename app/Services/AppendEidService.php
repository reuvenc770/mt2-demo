<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 10/21/16
 * Time: 3:08 PM
 */

namespace App\Services;
use App\Repositories\EmailRepo;
use Log;

class AppendEidService
{
    private $emailRepo;
    public function __construct(EmailRepo $emailRepo)
    {
        $this->emailRepo = $emailRepo;
    }

    public function createFile($rows){
       try{
           foreach($rows as $row){
               $emailAddress = $this->emailRepo->getEmaiAddress($row);
              //$feedId = $this->emailRepo->getCurrentAttributedFeedId($row);
               Log::info($emailAddress);
           }

       } catch (\Exception $e){
        Log::info($e);
       }

    }
}