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

elixir.extend( 'deploySassAndFonts' , function ( mix ) {
    mix.copy( 'node_modules/bootstrap-sass/assets/fonts/' , 'public/fonts' );
    mix.copy( 'resources/assets/fonts/open-sans/' , 'public/fonts/open-sans' );
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
        mix.deploySassAndFonts( mix );
        mix.deployTemplates( mix );
        mix.deployImages( mix );
        mix.runTdd( mix );
    } ,
    'runTdd' : function ( mix ) {
        mix.runTdd( mix );
    } ,
    'deploySass' : function ( mix ) {
        mix.deploySassAndFonts( mix );
    } ,
    'deployTemplates' : function ( mix ) {
        mix.deployTemplates( mix );
    } ,
    'deployImages' : function ( mix ) {
        mix.deployImages( mix );
    }
};

elixir( ( argv.run ? mt2TaskMap[ argv.run ] : mt2TaskMap.deployAll ) );
