<?php

namespace App\Http\Controllers;

use App\Events\BulkSuppressionFileWasUploaded;
use App\Services\EmailRecordService;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\BulkSuppressionRequest;
use Laracasts\Flash\Flash;
use App\Facades\Suppression;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;
use App\Services\SuppressionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;

class BulkSuppressionController extends Controller
{

    protected $api;
    protected $suppServ;
    protected $emailService;
    const BULK_SUPPRESSION_API_ENDPOINT = 'bulk_suppress_save';


    public function __construct(MT1ApiService $api, SuppressionService $suppServ , EmailRecordService $recordService)
    {
        $this->api = $api;
        $this->suppServ = $suppServ;
        $this->emailService = $recordService;
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
        $path = storage_path() . "/app/files/uploads/bulksuppression/$dateFolder/";
        $files = scandir($path);

        foreach ($files as $fileName) {
            if (!preg_match('/^\./', $fileName)) {
                Event::fire(new BulkSuppressionFileWasUploaded(
                    $request->input( 'reason' ) ,
                    $fileName ,
                    date('Ymd')
                ));
            }
        }

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
        $type = 'eid';
        $records = $request->input('emails');
        $reason = $request->input('suppressionReasonCode');
        $emails = [];

        if (preg_match("/@+/", $records)) $type = 'email';
        if(!empty($records)) {
            if ($type == "email") {
                foreach (explode(',', $records) as $record) {
                    $emails[] = $record;

                    Suppression::recordSuppressionByReason($record, Carbon::today()->toDateTimeString(), $reason);
                }
            } else {
                foreach (explode(',', $records) as $record) {
                    $email = $this->emailService->getEmailAddress($record);

                    $emails[] = $email;

                    Suppression::recordSuppressionByReason($email, Carbon::today()->toDateTimeString(), $reason);
                }
            }
        }

        return response()->json( [ 'status' => true ] , 200 );
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
