<?php
/**
 * @author Adam Chin <achin@zetaglobal.com>
 */

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class CustomRuleValidatorProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        \Validator::extend( 'euroDate' , '\\App\\Http\\Validators\\CustomValidatorHelper@europeanDate' );
        \Validator::extend( 'euroDateNotFuture' , '\\App\\Http\\Validators\\CustomValidatorHelper@europeanDateNotFuture' );
        \Validator::extend( 'hash' , '\\App\\Http\\Validators\\CustomValidatorHelper@hash' );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
