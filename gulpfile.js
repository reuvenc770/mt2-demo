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


elixir.extend( 'deploySass' , function ( mix ) {
    mix.sass('app.scss');
} );

elixir.extend( 'deployTemplates' , function ( mix ) {
    mix.copy( 'resources/assets/js/templates' , 'public/js/templates' );
} );

elixir.extend( 'deployImages' , function ( mix ) {
    mix.copy( 'resources/assets/img' , 'public/img' );
} );



elixir.extend( 'runTdd' , function ( mix ) {

} );

var mt2TaskMap = {
    'deployAll' : function ( mix ) {
        mix.deploySass( mix );
        mix.deployTemplates( mix );
        mix.deployImages( mix );
        mix.runTdd( mix );
    } ,
    'runTdd' : function ( mix ) {
        mix.runTdd( mix );
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
