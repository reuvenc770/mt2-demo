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
        "controller" : function () {} ,
        "controllerAs" : "ctrl" , 
        "bindToController" : { 
            "headers" : "=" ,
            "records" : "=" ,
            "editclickhandler" : "="
        } ,
        "templateUrl" : "js/templates/generic-table.html"
    };
} );

mt2App.directive( 'editButton' , function () {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "clickhandler" : "&" ,
            "recordid" : "="
        } ,
        "templateUrl" : "js/templates/edit-button.html"
    };
} );

mt2App.controller( 'espController' , [ '$window' , '$location' , '$timeout' , '$sce' , '$log' , 'EspApiService' , function ( $window , $location , $timeout , $sce ,$log , EspApiService ) {
    var self = this;

    self.headers = [ '' , 'ID' , 'ESP' , 'Account' , 'Created' , 'Updated' ];
    self.accounts = [];

    self.currentAccount = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/esp\/edit\/(\d{1,})/ );

        EspApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount.id = response.data.id;
            self.currentAccount.accountName = response.data.account_name;
            self.currentAccount.key1 = response.data.key_1;
            self.currentAccount.key2 = response.data.key_2;
        } )
    }

    self.loadAccounts = function () {
        EspApiService.getAccounts( function ( response ) {
            self.accounts = response.data;
        } );
    };

    self.viewAdd = function () {
        $window.location.href = "esp/create";
    };

    self.viewEdit = function () {
        $log.log( self.recordid );
        //$window.location.href = "esp/edit/"
    };

    self.saveNewAccount = function () {
        EspApiService.saveNewAccount( self.currentAccount );
    };

    self.editAccount = function () {
        EspApiService.editAccount( self.currentAccount );
    }
} ] );

mt2App.service( 'EspApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/esp';

    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback ,
                function ( response ) {
                    $log.log( response );
                }
            );
    }

    self.getAccounts = function ( successCallback ) {
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

    self.editAccount = function ( account ) {
        var request = account;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } );
    }
} );

//# sourceMappingURL=esp.js.map
