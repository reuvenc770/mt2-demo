/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [ 'ngMaterial' , 'ngclipboard' ] );

mt2App.config( function ( $locationProvider ) {
    $locationProvider.html5Mode( true );
} );
