<?php

namespace App\Http\Controllers;

use App\Facades\Suppression;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\ShowInfoRecordRequest;
use App\Http\Requests\ShowInfoSuppressRecordRequest;
use App\Http\Controllers\Controller;
use App\Services\MT1ApiService;

class ShowInfoController extends Controller
{
    const SHOW_INFO_ENDPOINT = 'show_info_2';
    const BULK_SUPPRESSION_API_ENDPOINT = 'bulk_suppressions';
    protected $emailService;
    protected $api;

    public function __construct ( EmailService $emailService, MT1ApiService $api) {
        $this->emailService = $emailService;
        $this->api = $api;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view( 'bootstrap.pages.show-info' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response( 'Unauthorized' , 401 );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShowInfoSuppressRecordRequest $request)
    {
        $type = 'eid';
        $records = $request->input('id');
        if ( preg_match( "/@+/" , $records ) ) $type = 'email';

        if($type == "email"){
            foreach(explode(',',$records) as $record) {
                Suppression::recordSuppressionByReason($record, Carbon::today()->toDateTimeString(), $request->input('selectedReason'));
            }
        }
        else {
            foreach(explode(',',$records) as $record) {
                $email = $this->emailService->getEmailAddress($record);
                Suppression::recordSuppressionByReason($email, Carbon::today()->toDateTimeString(), $request->input('selectedReason'));
            }
        }
        $payload = array(
            "emails" => $request->input('id'),
            'suppressionReasonCode' => 'MT2IM',
            'suppfile' => ''
        );
        return response( $this->api->getJSON( self::BULK_SUPPRESSION_API_ENDPOINT,
            $payload ) );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(ShowInfoRecordRequest $request , $ids)
    {
        if (preg_match('/\,+/', $ids)) {
            $ids = explode(',', $ids);
        }
        else {
            // no delimiter - one single id
            $ids = [$ids];
        }
        

        $records = [];
        $suppressions = [];

        foreach ($ids as $id) {
            $type = preg_match( "/@+/" , $id ) ? 'email' : 'eid';
            $data = $this->emailService->getRecordInfo($id, $type) ?: [];

            foreach($data as $record) {

                $records[] = [
                    'action' => $record['action'],
                    'action_date' => $record['action_date'],
                    'address' => $record['address'],
                    'birthdate' => $record['birthdate'],
                    'date' => $record['date'],
                    'eid' => $record['eid'],
                    'email_addr' => $record['email_address'],
                    'first_name' => $record['first_name'],
                    'gender' => $record['gender'],
                    'ip' => $record['ip'],
                    'last_name' => $record['last_name'],
                    'client' => $record['client_name'],
                    'removal_date' => $record['removal_date'],
                    'source_url' => $record['source_url'],
                    'status' => $record['status'],
                    'subscribe_datetime' => $record['subscribe_date'],
                    'suppressed' => $record['suppressed']

                ];
                if (1 === (int)$record['suppressed']) {
                    $suppressions[] = [
                        'email_addr' => $record['email_address'],
                        'suppressionReasonDetails' => $record['suppression_reason'],
                        'espAccountName' => '',
                        'campaignName' => ''
                    ];
                }
                
            }     
        }

        $output = [
            'data' => $records,
            'suppression' => $suppressions
        ];

        return response(json_encode($output));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response( 'Unauthorized' , 401 );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        return response( 'Unauthorized' , 401 );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return response( 'Unauthorized' , 401 );
    }
}
