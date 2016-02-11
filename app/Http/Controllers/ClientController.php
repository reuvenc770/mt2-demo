<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Services\NavigationService;

class ClientController extends Controller
{
    protected $mockClients = [
        [ 1 , 'Acxiom' , 'Active' , 'Do Not Mail' , 402833 , 'feed.acxiom.com' , 'Acxiom' , 'Direct' , 'jdoe' , 'jdoe@acxiom.com' , '888-888-8888' , '1 Main St, NY, NY 10002' , 'US' ] ,
        [ 2 , 'Tactara' , 'Active' , 'Misc' , 402738 , 'feed.tactara.com' , 'Tactara' , 'Direct' , 'ldoe' , 'ldoe@tactara.com' , '777-777-7777' , '1 Side St, NY, NY 10009' , 'US' ] ,
        [ 3 , 'Popular Marketing' , 'Paused' , 'Misc' , 402928 , 'feed.popularm.net' , 'Popular Marketing' , 'Direct' , 'ohpie' , 'ohpie@popularm.com' , '666-666-6666' , '1 Left St, NY, NY 10029' , 'US' ]
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json( $this->mockClients );
    }

    /**
     *
     */
    public function listAll () {
        return response()->view( 'pages.mocks.client-index' );
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        return response()->view( 'pages.mocks.client-edit' );
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
        //
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
