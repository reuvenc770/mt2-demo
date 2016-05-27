/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [ 'ngMaterial' , 'ngMessages' , 'ui.select' , 'flow' , 'ngclipboard' ] );

mt2App.config( function ( $locationProvider , $mdThemingProvider ) {
    $locationProvider.html5Mode( true );

    $mdThemingProvider.theme( 'mt2-zeta' , 'light' )
        .primaryPalette( 'indigo' )
        .accentPalette( 'deep-purple' )
        .warnPalette( 'deep-orange' )
        .backgroundPalette( 'grey' );
} );

mt2App.filter( 'bytes' , function() {
    return function( bytes , precision ) {
        if ( bytes === 0 || isNaN(parseFloat( bytes ) ) || !isFinite( bytes ) ) { return '-'; }
        if ( typeof precision === 'undefined' ) { precision = 1; }

        var units = [ 'bytes' , 'kB' , 'MB' , 'GB' , 'TB' , 'PB' ] ,
        number = Math.floor( Math.log( bytes ) / Math.log( 1024 ) );

        return ( bytes / Math.pow( 1024 , Math.floor( number ) ) ).toFixed( precision ) +  ' ' + units[ number ];
    }
} );
