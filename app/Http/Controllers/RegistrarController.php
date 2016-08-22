<?php

namespace App\Http\Controllers;

use App\Services\RegistrarService;
use Illuminate\Http\Request;

use App\Http\Requests;
use Log;
use Laracasts\Flash\Flash;
use AdrianMejias\States\States;
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
            ->view('pages.registrar.registrar-index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $registrars = $this->registrarService->getAll();
        $return = array();
        foreach ($registrars as $registrar) {
            $return[] = array(
                $registrar->id,
                $registrar->name,
                $registrar->username

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
        $states = States::all();
        return view('pages.registrar.registrar-add', ['states' => $states]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Requests\AddRegistrarRequest $request)
    {
        Flash::success("Registrar was Successfully Created");
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
        $states = States::all();
        return response()
            ->view( 'pages.registrar.registrar-edit', ['states' => $states]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\EspApiEditRequest  $request
     * @param  int  $id The ESP Account ID being updated.
     * @return \Illuminate\Http\Response
     */
    public function update(Requests\EditRegistrarRequest $request, $id)
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
    public function destroy(Request $request, $id)
    {
        $this->registrarService->toggleRow($id,$request->get("direction"));
    }
}
