<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\EspService;
use App\Http\Requests\EspAddRequest;
use App\Http\Requests\EspEditRequest;
use Laracasts\Flash\Flash;

class EspController extends Controller
{
    protected $espService;


    public function __construct ( EspService $espService ) {
        $this->espService = $espService;
    }

    /**
     * Display a listing of ESP Accounts.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $accounts = $this->espService->getAllEsps();

        return response()->json( $accounts );
    }



    /**
     * Show the ESP Account index page.
     *
     * @return \Illuminate\Http\Response
     */
    public function listAll ()
    {
        return response()
            ->view( 'bootstrap.pages.esp.esp-index' );
    }

    /**
     * Show the form for creating a new ESP Account.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return response()
            ->view( 'bootstrap.pages.esp.esp-add' , ['formType' => 'add' ] );
    }

    /**
     * Store a newly created ESP Account.
     *
     * @param  EspAddRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EspAddRequest $request)
    {
        Flash::success("ESP Account was Successfully Added");
        $request = $this->espService->insertRow( $request->all() );
        return response()->json( [ 'status' => $request ] );
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
        return response()
            ->view( 'bootstrap.pages.esp.esp-edit' , ['formType' => 'edit' ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EspApiEditRequest  $request
     * @param  int  $id The ESP Account ID being updated.
     * @return \Illuminate\Http\Response
     */
    public function update(EspEditRequest $request, $id)
    {
        $this->espService->updateAccount( $id , $request->toArray() );
        Flash::success("ESP Account was Successfully Updated");
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
