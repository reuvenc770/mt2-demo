<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Services\CpmPricingService;

class CpmPricingController extends Controller
{
    protected $pricing;

    public function __construct ( CpmPricingService $pricing ) {
        $this->pricing = $pricing;
    }

    public function listAll () {
        return response()->view( 'pages.cpm.cpm-pricing-index' );
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json( $this->pricing->getPricings() );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $status = $this->pricing->create( $request->all() );

        response()->json( $status );
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
        $status = $this->pricing->update( $id , $request->all() );

        response()->json( $status );
    }
}
