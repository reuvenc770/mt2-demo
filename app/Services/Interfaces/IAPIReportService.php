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
   public function retrieveReportStats($date);
   public function insertRawStats($data);
   public function mapToStandardReport($data);
}
