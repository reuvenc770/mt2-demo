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
   public function retrieveAPIReportStats($date);
   public function insertAPIRawStats($data);
   public function insertCSVRawStats($data);
   public function mapToStandardReport($data);
   public function mapToRawReport($data);
}