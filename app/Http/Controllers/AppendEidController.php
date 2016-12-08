<?php

namespace App\Http\Controllers;
use App\Jobs\AppendEidEmail;
use App\Services\AppendEidService;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;

use App\Http\Requests;
use League\Csv\Reader;
class AppendEidController extends Controller
{
    use DispatchesJobs;
    private $appendEidService;

    public function __construct(AppendEidService $service)
    {
        $this->appendEidService = $service;
    }

    public function index(){
       return view('bootstrap.pages.appendeid.append-eid-index');
   }

    public function manageUpload(Request $request){

        $fileName = $request->input("fileName");
        $email = $request->input("email");
        $includeFeed = (bool)$request->input('feed');
        $includeFields = (bool)$request->input('fields');
        $includeSuppression = (bool)$request->input('suppress');
        $dateFolder = date('Ymd');
        $path = storage_path() . "/app/files/uploads/appendEID/$dateFolder/$fileName";
        $this->dispatch(new AppendEidEmail($path,$fileName,$includeFeed,$includeFields,$includeSuppression));


        return response()->json(["success" => true]);
    }
}
