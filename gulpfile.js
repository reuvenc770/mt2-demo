var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.sass('app.scss');

    mix.copy( 'node_modules/bootstrap-sass/assets/fonts/' , 'public/fonts' );

    mix.copy( 'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js' , 'public/js/bootstrap.min.js' );

    mix.copy( 'node_modules/angular/angular.min.js' , 'public/js/angular.min.js' );

    mix.copy( 'node_modules/jquery/dist/jquery.min.js' , 'public/js/jquery.min.js' );

    //ESP Angular Dependencies
    mix.scripts( [ 'app.js' , 'mt2app/GenericTableDirective.js' , 'mt2app/EditButtonDirective.js'] , 'public/js/angular_base.js' );
    mix.scripts( [  'esp/EspController.js' , 'esp/EspApiService.js' ] , 'public/js/esp.js' );
    mix.scripts( [  'user/UserController.js' , 'user/UserApiService.js' ] , 'public/js/user.js' );
    mix.scripts( [  'role/RoleController.js' , 'role/RoleApiService.js' ] , 'public/js/role.js' );
    mix.scripts( [  'job/JobController.js' , 'job/JobApiService.js' ] , 'public/js/job.js' );
    mix.copy( 'resources/assets/js/templates' , 'public/js/templates' );
});
