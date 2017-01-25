<?php

namespace App\Models\Interfaces;

use App\Models\Interfaces\IReport;

interface IReportMapper extends IReport
{
    public function getDateFieldName();

    public function getSubjectFieldName();
}