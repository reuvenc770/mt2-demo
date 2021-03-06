<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\EspApiAccountService;
use App\Services\EspService;
use App\Http\Requests\EspApiAddRequest;
use App\Http\Requests\EspApiEditRequest;
use Laracasts\Flash\Flash;
class EspApiAccountController extends Controller
{
    protected $espService;

    protected $espAccountService;

    public function __construct (EspService $espService , EspApiAccountService $espAccountService ) {
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
        $accounts = $this->espAccountService->getAllActiveAccounts();

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
        return  response()->json( array_values( $this->espAccountService->getAllAccounts()->toArray() ));
    }

    public function returnAllActive(){
        return  response()->json( array_values( $this->espAccountService->getAllActiveAccounts()->toArray() ));
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
        return response()
            ->view( 'pages.espapi.esp-add' , [ 'espList' => $this->getEspList() , 'formType' => 'add' ] );
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

        $customIdHistory = $this->espAccountService->getCustomIdHistoryByEsp( $id );

        return response()
            ->view( 'pages.espapi.esp-edit' , [
                'accountId' => $account->id ,
                'espName' => $account->esp->name ,
                'accountName' => $account->account_name ,
                'customId' => $account->custom_id,
                'key1' => $account->key_1 ,
                'key2' => $account->key_2 ,
                'espList' => $this->getEspList() ,
                'formType' => 'edit' ,
                'customIdHistory' => $customIdHistory
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
    public function destroy(Request $request, $id)
    {
    }

    public function toggleStats ( Request $request , $id ) {
        $this->espAccountService->toggleStats( $id , $request->input( 'currentStatus' ) );
    }

    public function toggleSuppression ( Request $request , $id ) {
        $this->espAccountService->toggleSuppression( $id , $request->input( 'currentStatus' ) );
    }

    public function activate ( $id ) {
        $this->espAccountService->activate( $id );
    }

    public function deactivate ( $id ) {
        $this->espAccountService->deactivate( $id );
    }

    public function displayEspAccounts(Request $request, $name){
        return $this->espAccountService->getAllAccountsByESPName($name);
    }


    public function grabTemplatesByESP($id){
        $data = $this->espAccountService->getTemplatesByEspId($id);
        return  response()->json($data);
    }

    public function generateCustomId(){
        return response()->json( $this->espAccountService->generateCustomId() );
    }

    protected function getEspList() {

        $esps = $this->espService->getAllEsps();

        $espList = array();
        foreach ( $esps as $esp ) {
            $espList[ $esp->id ] = $esp->name;
        }

        return $espList;
    }
}
