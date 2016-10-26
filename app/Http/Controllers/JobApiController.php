<?php

namespace App\Http\Controllers;

use App\Services\JobEntryService;
use App\Models\JobEntry;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
class JobApiController extends Controller
{
    protected $jobService;
    public function __construct(JobEntryService $jobEntryService)
    {
        $this->jobService = $jobEntryService;
    }

    public function index()
    {
        return $this->jobService->getTrailingLogList();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listAll() {
        return response()
            ->view( 'bootstrap.pages.devtools.job-index' , ['statusNames'=>JobEntry::getPrettyStatusNames()] );
    }


}
