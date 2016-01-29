;(function(){

/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [ 'mt2Esp' ] );

mt2App.config( function ( $locationProvider ) {
    $locationProvider.html5Mode( true );
} );

mt2App.directive( 'genericTable' , function () {
    return {
        "restrict" : 'E' ,
        //"transclude" : true ,
        "template" : '<table class="table table-striped table-bordered table-hover text-center"><generic-table-head></generic-table-head></table>'
    
    //<generic-table-head></generic-table-head><generic-table-body></generic-table-body></table>'
    };        
} );

mt2App.directive( 'genericTableHead' , function () {
    return {
        "restrict" : "E" ,
        //"transclude" : true ,
        "template" : '<thead><tr></tr></thead>'

//<generic-table-headers></generic-table-headers></tr></thead>'
    };
} );

mt2App.directive( 'genericTableHeaders' , function () {
    return {
        "restrict" : "E" ,
        //"transclude" : true ,
        "template" : '<th></th>'
    };
} );

mt2App.directive( 'genericTableBody' , function () {
    return {
        "restrict" : "E" , 
        //"transclude" : true ,
        "template" : '<tbody><generic-table-records></generic-table-records></tbody>'
    };
} );

mt2App.directive( 'genericTableRecords' , function () {
    return {
        "restrict" : "E" ,
        //"transclude" : true ,
        "template" : '<tr><tr>'
    };
} );

/**
 * ESP Module
 */
var mt2Esp = angular.module( 'mt2Esp' , [] );

mt2Esp.config( function () {} );

mt2Esp.controller( 'Mt2EspController' , [ 'Mt2EspApiService' , function ( apiService ) {
    
    this.espList = [];
} ] );

mt2Esp.directive( 'espTable' , function () {
    return {
        "controller" : "Mt2EspController" ,
        "controllerAs" : "ctrl" ,
        "bindToController" : true ,
        "restrict" : "E" ,
        //"transclude" : true ,
        "template" : '<generic-table></generic-table>'
    };
} );

mt2Esp.service( 'Mt2EspApiService' , function ( $http ) {
    this.baseApiUrl = '/api/esp';

    this.getList = function () {
        var defer = $q.defer();

        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then(
                function success ( response ) {
                    defer.resolve( response );
                } ,
                function error ( response ) {
                    defer.reject( response );
                }
            );

        return defer.promise();
    }
} );

} )();
