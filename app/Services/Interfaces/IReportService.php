<?php
/**
 * Created by PhpStorm.
 * User: pcunningham
 * Date: 1/14/16
 * Time: 1:44 PM
 */

namespace App\Services\Interfaces;


interface IReportService
{
   public function retrieveReportStats($date);
}