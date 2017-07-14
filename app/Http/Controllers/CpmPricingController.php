<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Cache;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Cache::tags( 'Builder' )->flush();

        $result = $this->pricing->create( $request->all() );

        return response()->json( $result , ( $result[ 'status' ] === false ? 422 : 200 ) );
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
        Cache::tags( 'Builder' )->flush();

        $result = $this->pricing->update( $id , $request->all() );

        return response()->json( $result , ( $result[ 'status' ] === false ? 422 : 200 ) );
    }
}
