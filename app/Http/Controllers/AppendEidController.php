<?php

namespace App\Http\Controllers;

use App\Services\AppendEidService;
use Illuminate\Http\Request;

use App\Http\Requests;
use League\Csv\Reader;
class AppendEidController extends Controller
{
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
        $returnData = array();
        $dateFolder = date('Ymd');
        $path = storage_path() . "/app/files/uploads/appendEID/$dateFolder/$fileName";

        $reader = Reader::createFromPath($path);
        $flag = false;
        $results = $reader->fetchAssoc(["eid"]);
        $file = $this->appendEidService->createFile($results);
        return response()->json($returnData);
    }
}
