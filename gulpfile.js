var elixir = require('laravel-elixir');
var argv = require( 'yargs' ).argv;

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

elixir.extend( 'deployNodeModules' , function ( mix ) {
    mix.copy( 'node_modules/bootstrap-sass/assets/fonts/' , 'public/fonts' );

    mix.copy( 'node_modules/bootstrap-sass/assets/javascripts/bootstrap.min.js' , 'public/js/bootstrap.min.js' );

    mix.copy( 'node_modules/jasny-bootstrap/dist/js/jasny-bootstrap.min.js' , 'public/js/jasny-bootstrap.min.js' );

    mix.copy( 'node_modules/angular/angular.min.js' , 'public/js/angular.min.js' );

    mix.copy( 'node_modules/jquery/dist/jquery.min.js' , 'public/js/jquery.min.js' );

    mix.copy( 'node_modules/angular-material/angular-material.min.js' , 'public/js/angular-material.min.js' );

    mix.copy( 'node_modules/angular-aria/angular-aria.min.js' , 'public/js/angular-aria.min.js' );

    mix.copy( 'node_modules/angular-animate/angular-animate.min.js' , 'public/js/angular-animate.min.js' );

    mix.copy( 'node_modules/angular-messages/angular-messages.min.js' , 'public/js/angular-messages.min.js' );

    mix.copy( 'node_modules/ngclipboard/dist/ngclipboard.min.js' , 'public/js/ngclipboard.min.js' );

    mix.copy( 'node_modules/clipboard/dist/clipboard.min.js' , 'public/js/clipboard.min.js' );

    mix.copy( 'node_modules/moment/min/moment-with-locales.min.js' , 'public/js/momment-with-locales.min.js' );

    mix.copy( 'node_modules/ui-select/dist/select.min.js' , 'public/js/select.min.js' );

    mix.copy( 'node_modules/ui-select/dist/select.min.css' , 'public/css/select.min.css' );

    mix.copy( 'node_modules/ng-flow/dist/ng-flow-standalone.min.js', 'public/js/ng-flow-standalone.min.js' );

    mix.copy( 'node_modules/angular-ivh-treeview/dist/ivh-treeview.min.js' , 'public/js/ivh-treeview.min.js' );

    mix.copy( 'node_modules/angular-drag-and-drop-lists/angular-drag-and-drop-lists.min.js' , 'public/js/angular-drag-and-drop-lists.min.js' );

    mix.copy( 'node_modules/angular-material-data-table/dist/md-data-table.min.js' , 'public/js/md-data-table.min.js' );
    mix.copy('node_modules/angu-complete/angucomplete-alt.js', 'public/js/angucomplete-alt.js');
    mix.copy('node_modules/angular-cookies/angular-cookies.min.js', 'public/js/angular-cookies.min.js');
} );

elixir.extend( 'deploySass' , function ( mix ) {
    mix.sass('app.scss');
} );

elixir.extend( 'deployTemplates' , function ( mix ) {
    mix.copy( 'resources/assets/js/templates' , 'public/js/templates' );
    mix.copy( 'resources/assets/js/bootstrap/templates' , 'public/js/bootstrap/templates' );
} );

elixir.extend( 'deployImages' , function ( mix ) {
    mix.copy( 'resources/assets/img' , 'public/img' );
} );

elixir.extend( 'runTdd' , function ( mix ) {

} );

var mt2TaskMap = {
    'deployAll' : function ( mix ) {
        mix.deployNodeModules( mix );
        mix.deploySass( mix );
        mix.deployTemplates( mix );
        mix.deployImages( mix );
        mix.runTdd( mix );
    } ,
    'runTdd' : function ( mix ) {
        mix.runTdd( mix );
    } ,
   'deployNodeModules' : function ( mix ) {
        mix.deployNodeModules( mix );
    } ,
    'deploySass' : function ( mix ) {
        mix.deploySass( mix );
    } ,
    'deployTemplates' : function ( mix ) {
        mix.deployTemplates( mix );
    } ,
    'deployImages' : function ( mix ) {
        mix.deployImages( mix );
    }
};

elixir( ( argv.run ? mt2TaskMap[ argv.run ] : mt2TaskMap.deployAll ) );
