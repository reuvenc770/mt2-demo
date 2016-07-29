<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\EspApiAccountService;
use App\Services\EspApiService;
use App\Http\Requests\EspApiAddRequest;
use App\Http\Requests\EspApiEditRequest;
use Laracasts\Flash\Flash;
class EspApiController extends Controller
{
    protected $espService;

    protected $espAccountService;

    public function __construct ( EspApiService $espService , EspApiAccountService $espAccountService ) {
        $this->espService = $espService;
        $this->espAccountService = $espAccountService;
    }

    /**
     * Display a listing of ESP Accounts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = $this->espAccountService->getAllAccounts();

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

    public function returnAll(){
        return  response()->json($this->espAccountService->getAllAccounts());
    }

    /**
     * Show the ESP Account index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function listAll ()
    {
        return response()
            ->view( 'pages.espapi.esp-index' );
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
            ->view( 'pages.espapi.esp-add' , [ 'espList' => $espList ] );
    }

    /**
     * Store a newly created ESP Account.
     *
     * @param  EspApiAddRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EspApiAddRequest $request)
    {
        Flash::success("API Account was Successfully Added");
        $this->espAccountService->saveAccount( $request->all() );
    }

    /**
     * Display the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to lookup.
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->espAccountService->getAccount( $id );

    }

    /**
     * Show the form for editing the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to edit.
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        $account = $this->espAccountService->getAccountAndEsp( $id );
        if(is_null($account)){
            Flash::error("{$id} does not exist");
            return redirect("/espapi");
        }
        return response()
            ->view( 'pages.espapi.esp-edit' , [
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
     * @param  \App\Http\Requests\EspApiEditRequest  $request
     * @param  int  $id The ESP Account ID being updated.
     * @return \Illuminate\Http\Response
     */
    public function update(EspApiEditRequest $request, $id)
    {
        $this->espAccountService->updateAccount( $id , $request->toArray() );
        Flash::success("API Account was Successfully Updated");
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

    public function displayEspAccounts(Request $request, $name){
        return $this->espAccountService->getAllAccountsByESPName($name);
    }
}
