<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use App\Http\Requests;
use Sentinel;
use Cache;
class CacheController extends Controller
{
    public function clearCacheTag(Request $request){

        if(Sentinel::hasAccess("tools.cache")){
            Cache::tags($request->get('cacheTag'))->flush();
            Flash::success( "{$request->get('cacheTag')} is cleared" );
            return back();
        } else {
            Flash::success( 'You do not have permission to clear the cache.. How did you get here' );
            return response()->view( "bootstrap.pages.attribution.attribution-index" );
        }
    }
}
