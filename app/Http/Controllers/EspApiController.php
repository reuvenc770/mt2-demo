<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\ESPAccountService;

class EspApiController extends Controller
{
    protected $espService;

    public function __construct ( ESPAccountService $service ) {
        $this->espService = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = $this->espService->getAllAccounts();

        $accountList = [];

        foreach ( $accounts as $account ) {
            $accountList []= [
                $account->id ,
                $account->esp ,
                $account->account_name ,
                $account->created_at ,
                $account->updated_at
            ];
        }

        return response()->json( $accountList );
    }

    /**
     *
     */
    public function list ()
    {
        return response()
            ->view( 'pages.esp.esp-index' );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $esps = $this->espService->getAllEsps();

        $espList = array();
        foreach ( $esps as $esp ) {
            $espList[ $esp->id ] = $esp->name;
        }

        return response()
            ->view( 'pages.esp.esp-add' , [ 'espList' => $espList ] );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        return $this->espService->saveAccount( $request->all() );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->espService->getAccount( $id );

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        $account = $this->espService->getAccount( $id );
        $esp = $this->espService->getEsp( $account->esp_id );

        return response()
            ->view( 'pages.esp.esp-edit' , [
                'accountId' => $account->id ,
                'espName' => $esp->name ,
                'accountName' => $account->account_name ,
                'key1' => $account->key_1 ,
                'key2' => $account->key_2
            ] );
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
        $this->espService->updateAccount( $id , $request );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
