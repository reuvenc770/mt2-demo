/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [
    'ngMaterial' ,
    'ngMessages' ,
    'ngCookies' ,
    'angucomplete-alt',
    'ui.select' ,
    'flow' ,
    'ngclipboard' ,
    'ivh.treeview' ,
    'md.data.table'
] );

mt2App.config( function ( $locationProvider , $mdThemingProvider , ivhTreeviewOptionsProvider ) {
    $locationProvider.html5Mode( true );

    var mt2Primary = $mdThemingProvider.extendPalette('indigo' , {
        '500' : '16416c'
    });

    var mt2Warn = $mdThemingProvider.extendPalette('deep-orange', {
        "500" : '#FFA726',
        "contrastDefaultColor" : 'light'
    });

    var mt2Background = $mdThemingProvider.extendPalette('grey' , {
        '800' : '383F47'
    });

    $mdThemingProvider.definePalette('mt2-primary', mt2Primary );
    $mdThemingProvider.definePalette('mt2-warn', mt2Warn);
    $mdThemingProvider.definePalette('mt2-background', mt2Background );

    $mdThemingProvider.theme( 'mt2-zeta' , 'dark' )
        .primaryPalette( 'mt2-primary' , {
            'hue-1' : '200'
        } )
        .accentPalette( 'blue' )
        .warnPalette( 'mt2-warn')
        .backgroundPalette( 'mt2-background' , {
            "default" : '50' ,
            "hue-1" : '100' ,
            "hue-2" : '400' ,
            "hue-3" : '800'
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

mt2App.filter('limitObjects', function() {
    return function( object , limit ) {
        console.log(object);
        console.log(limit);
    }
});

