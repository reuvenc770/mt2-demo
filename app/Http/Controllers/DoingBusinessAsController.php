<?php

namespace App\Http\Controllers;


use AdrianMejias\States\States;
use App\Services\DoingBusinessAsService;
use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;

class DoingBusinessAsController extends Controller
{
    protected $doingBusinessService;
    protected $states;
    public function __construct(DoingBusinessAsService $doingBusinessService, States $states){
        $this->doingBusinessService = $doingBusinessService;
        $this->states = $states;
    }

    public function listAll()
    {
        return response()
            ->view('pages.dba.dba-index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $dbas = $this->doingBusinessService->getAll();
        return response()->json($dbas);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $states = States::all();
        return view('pages.dba.dba-add', array("states" => $states));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddDBARequest $request)
    {
        Flash::success("DBA was Successfully Created");
        $request = $this->doingBusinessService->insertRow($request->all());
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
        return $this->doingBusinessService->getDBA( $id );

    }

    /**
     * Show the form for editing the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to edit.
     * @return \Illuminate\Http\Response
     */
    public function edit( )
    {
        $states = States::all();
        return response()
            ->view( 'pages.dba.dba-edit', array("states" => $states));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EspApiEditRequest  $request
     * @param  int  $id The ESP Account ID being updated.
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditDBARequest $request, $id)
    {
        $this->doingBusinessService->updateAccount( $id , $request->toArray() );
        Flash::success("DBA Account was Successfully Updated");
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
