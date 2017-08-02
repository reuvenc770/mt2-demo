<?php

namespace App\Http\Controllers;

use App\Events\BulkSuppressionFileWasUploaded;
use App\Services\EmailRecordService;
use Illuminate\Http\Request;
use App\Jobs\S3RedshiftExportJob;
use Illuminate\Foundation\Bus\DispatchesJobs;

use App\Http\Requests;
use App\Http\Requests\BulkSuppressionRequest;
use Laracasts\Flash\Flash;
use App\Facades\Suppression;
use App\Http\Controllers\Controller;
use App\Services\SuppressionService;
use App\Services\GlobalSuppressionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Artisan;
use Storage;

class BulkSuppressionController extends Controller
{
    use DispatchesJobs;

    protected $suppServ;
    protected $emailService;
    const BULK_SUPPRESSION_API_ENDPOINT = 'bulk_suppress_save';
    private $globalSuppService;


    public function __construct(SuppressionService $suppServ , EmailRecordService $recordService, GlobalSuppressionService $globalSuppService)
    {
        $this->suppServ = $suppServ;
        $this->emailService = $recordService;
        $this->globalSuppService = $globalSuppService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->view('pages.bulk-suppression');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Transfer locally-stored suppression uploads to MT1Bin
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $failed = [];
        $dateFolder = date('Ymd');
        $storagePath = "files/uploads/bulksuppression/$dateFolder/";
        $fullPath = storage_path() . "/app/" . $storagePath;
        $files = scandir($fullPath);
        $reasonId = $request->input('reason');
        $count = 0;

        foreach ($files as $fileName) {
            if (!preg_match('/^\./', $fileName)) {
                Event::fire(new BulkSuppressionFileWasUploaded(
                    $request->input( 'reason' ) ,
                    $fileName ,
                    date('Ymd')
                ));
            }

            $records = Storage::get($storagePath . $fileName);
            $split = (preg_match("/,/", $records)) ? ',' : "\n";
            $count += $this->suppressRecords($records, $reasonId, $split);
        }

        if ($count > 0) {
            $this->dispatchSuppressionUpdateJob();
        }

        Artisan::queue( 'mt1Import' , [
            'type' => 'globalSuppression' ,
            '--delay' => 10 
        ] );

        return $failed;
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(BulkSuppressionRequest $request)
    {
        $records = $request->input('emails');
        $reasonId = $request->input('suppressionReasonCode');
        $count = $this->suppressRecords($records, $reasonId, ',');

        if ($count > 0) {
            $this->dispatchSuppressionUpdateJob();
        }
        
        return response()->json( [ 'status' => true ] , 200 );
    }

    private function suppressRecords($records, $reasonId, $split) {
        $count = 0;

        if (!empty($records)) {
            $type =  (preg_match("/@+/", $records)) ? 'email': 'eid';
            $timestamp = Carbon::now()->toDateTimeString();

            foreach(explode($split, $records) as $record) {
                if ($type == "email") {
                    Suppression::recordSuppressionByReason($record, $timestamp, $reasonId);
                    $this->globalSuppService->insertSuppression($record, $timestamp, $reasonId);
                }
                else {
                    $email = $this->emailService->getEmailAddress($record);
                    Suppression::recordSuppressionByReason($email, $timestamp, $reasonId);
                    $this->globalSuppService->insertSuppression($email, $timestamp, $reasonId);
                }

                $count++;
            }
        }

        return $count;
    }

    private function dispatchSuppressionUpdateJob() {
        $version = 0;
        $tracking = str_random(16);
        $job = new S3RedshiftExportJob('SuppressionGlobalOrange', $version, $tracking);
        $this->dispatch($job);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
