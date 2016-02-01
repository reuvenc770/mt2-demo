;(function(){

/**
 * MT2 App Module
 */
var mt2App = angular.module( 'mt2App' , [] );

mt2App.config( function ( $locationProvider ) {
    $locationProvider.html5Mode( true );
} );

mt2App.directive( 'genericTable' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} , //"genericTableController" ,
        "controllerAs" : "ctrl" , 
        "bindToController" : { 
            "headers" : "=" ,
            "records" : "="
        } ,
        "templateUrl" : "js/templates/generic-table.html"
    };
} );

mt2App.controller( 'espController' , [ '$window' , '$timeout' , '$log' , 'EspApiService' , function ( $window , $timeout , $log , EspApiService ) {
    var self = this;

    self.headers = [ 'ESP' , 'Account' , 'Created' ];
    self.accounts = {};

    self.newAccount = { "espName" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.loadAccounts = function () {
        EspApiService.getAccounts( function ( response ) { self.accounts = response.data; } );
    };

    self.viewAdd = function () {
        $window.location.href = "esp/add";
    };

    self.saveNewAccount = function () {
        $log.log( self.newAccount );

        EspApiService.saveNewAccount( self.newAccount );
    };

    $timeout( self.loadAccounts() , 1000 );
} ] );

mt2App.service( 'EspApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/esp';

    self.getAccounts = function ( successCallback  ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback ,
                function ( response ) {
                    $log.log( response );
                }
            );
    }

    self.saveNewAccount = function ( newAccount ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newAccount
        } );
    }
} );

} )();
