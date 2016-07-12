/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [
    'ngMaterial' ,
    'ngMessages' ,
    'ui.select' ,
    'flow' ,
    'ngclipboard' ,
    'ivh.treeview' , 
    'dndLists'
] );

mt2App.config( function ( $locationProvider , $mdThemingProvider , ivhTreeviewOptionsProvider ) {
    $locationProvider.html5Mode( true );

    $mdThemingProvider.theme( 'mt2-zeta' , 'light' )
        .primaryPalette( 'indigo' )
        .accentPalette( 'blue' )
        .warnPalette( 'deep-orange' )
        .backgroundPalette( 'blue-grey' , {
            "default" : '50' ,
            "hue-1" : '200' ,
            "hue-2" : '400' ,
            "hue-3" : '700'
        } );

    $mdThemingProvider.setDefaultTheme( 'mt2-zeta' );

    ivhTreeviewOptionsProvider.set( {
        "expandToDepth" : 1 ,
        "twistieCollapsedTpl" : '<md-icon md-svg-icon="img/icons/ic_chevron_right_black_24px.svg"></md-icon>',
        "twistieExpandedTpl" : '<md-icon md-svg-icon="img/icons/ic_expand_more_black_24px.svg"></md-icon>',
        "twistieLeafTpl" : '<span style="cursor: default;">&#8192;&#8192;</span>'
    } );
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
