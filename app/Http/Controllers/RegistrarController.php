<?php

namespace App\Http\Controllers;

use App\Services\RegistrarService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Log;
use Laracasts\Flash\Flash;
class RegistrarController extends Controller
{
    protected $registrarService;

    public function __construct(RegistrarService $registrarService)
    {
        $this->registrarService = $registrarService;
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
        $dbas = $this->registrarService->getAll();
        $return = array();
        foreach ($dbas as $dba) {
            $return[] = array(
                $dba->id,
                $dba->name,
                $dba->state_id

            );
        }
        return response()->json($return);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.dba.dba-add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Flash::success("DBA was Successfully Created");
        $request = $this->registrarService->insertRow($request->all());
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
        return $this->registrarService->getRegistrar( $id );

    }

    /**
     * Show the form for editing the specified ESP Account.
     *
     * @param  int  $id The ESP Account ID to edit.
     * @return \Illuminate\Http\Response
     */
    public function edit( )
    {
        return response()
            ->view( 'pages.dba.dba-edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EspApiEditRequest  $request
     * @param  int  $id The ESP Account ID being updated.
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->registrarService->updateAccount( $id , $request->toArray() );
        Flash::success("Registrar Account was Successfully Updated");
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
