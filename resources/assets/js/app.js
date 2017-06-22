/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [
    'ngMessages' ,
    'ngCookies' ,
    'angucomplete-alt',
    'ui.select' ,
    'ngMaterial',
    'flow' ,
    'ngclipboard' ,
    'ivh.treeview' ,
    'md.data.table',
    'headroom' ,
    'dndLists'
] );

mt2App.config( function ( $locationProvider ,$mdThemingProvider, ivhTreeviewOptionsProvider , $qProvider ) {
    $locationProvider.html5Mode( true );
    $mdThemingProvider.generateThemesOnDemand( true );
    //Need to replace
    ivhTreeviewOptionsProvider.set( {
        "expandToDepth" : 1 ,
        "twistieCollapsedTpl" : '<md-icon md-svg-icon="img/icons/ic_chevron_right_black_24px.svg"></md-icon>',
        "twistieExpandedTpl" : '<md-icon md-svg-icon="img/icons/ic_expand_more_black_24px.svg"></md-icon>',
        "twistieLeafTpl" : '<span style="cursor: default;">&#8192;&#8192;</span>'
    } );

    $qProvider.errorOnUnhandledRejections(false);
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

mt2App.filter('limitObjects', [function(){
    return function(obj, limit){
        var keys = Object.keys(obj);
        if(keys.length < 1){
            return [];
        }
        var ret = new Object,
            count = 0;
        angular.forEach(keys, function(key, arrayIndex){
            if(count >= limit){
                return false;
            }
            ret[key] = obj[key];
            count++;
        });
        return ret;
    };
}]);
