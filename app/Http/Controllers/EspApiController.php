<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\ESPAccountService;
use App\Http\Requests\EspAddRequest;
use App\Http\Requests\EspEditRequest;

class EspApiController extends Controller
{
    protected $espService;

    public function __construct ( ESPAccountService $service ) {
        $this->espService = $service;
    }

    /**
     * Display a listing of ESP Accounts.
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
                $account->esp->name ,
                $account->account_name ,
                $account->created_at->toDayDateTimeString() ,
                $account->updated_at->toDayDateTimeString()
            ];
        }

        return response()->json( $accountList );
    }

    /**
     * Show the ESP Account index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function list ()
    {
        return response()
            ->view( 'pages.esp.esp-index' );
    }

    /**
     * Show the form for creating a new ESP Account
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
     * Store a newly created ESP Account.
     *
     * @param  \App\Http\EspAddRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EspAddRequest $request)
    {
        return $this->espService->saveAccount( $request->all() );
    }

    /**
     * Display the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to lookup.
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->espService->getAccount( $id );

    }

    /**
     * Show the form for editing the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to edit.
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        $account = $this->espService->getAccountAndEsp( $id );

        return response()
            ->view( 'pages.esp.esp-edit' , [
                'accountId' => $account->id ,
                'espName' => $account->esp->name ,
                'accountName' => $account->account_name ,
                'key1' => $account->key_1 ,
                'key2' => $account->key_2
            ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EspEditRequest  $request
     * @param  int  $id The ESP Account ID being updated.
     * @return \Illuminate\Http\Response
     */
    public function update(EspEditRequest $request, $id)
    {
        $this->espService->updateAccount( $id , $request );
    }

    /**
     * Remove the specified ESP Account from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Will not be in use. We don't want to delete ESP Accounts.
    }
}
