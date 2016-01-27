<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 1:44 PM
 */

namespace App\Services\Interfaces;


interface IAPIReportService
{
   public function retrieveApiReportStats($date);
   public function insertApiRawStats($data);
   public function insertCsvRawStats($data);
   public function mapToStandardReport($data);
}
