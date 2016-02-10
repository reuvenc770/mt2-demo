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
            "editurl" : "="
        } ,
        "templateUrl" : "js/templates/generic-table.html"
    };
} );

mt2App.directive( 'editButton' , [ '$window' , '$location' , function ( $window , $location ) {
    return {
        "scope" : {} ,
        "controller" : function () {} ,
        "controllerAs" : "ctrl" ,
        "bindToController" : {
            "editurl" : "=" ,
            "recordid" : "="
        } ,
        "templateUrl" : "js/templates/edit-button.html" ,
        "link" : function ( scope , element , attrs )  {
            if ( typeof( scope.ctrl ) != 'undefined' ) {
                element.on( 'click' , function () {
                    var fullEditUrl = scope.ctrl.editurl + scope.ctrl.recordid;
                    $location.url( fullEditUrl );
                    $window.location.href = fullEditUrl;
                } );
            }
        }
    };
} ] );

mt2App.controller( 'espController' , [ '$log' , '$window' , '$location' , '$timeout' , 'EspApiService' , function ( $log , $window , $location , $timeout , EspApiService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID' , 'ESP' , 'Account' , 'Created' , 'Updated' ];
    self.accounts = [];

    self.currentAccount = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.createUrl = 'esp/create/';
    self.editUrl = 'esp/edit/';

    self.formErrors = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/espapi\/edit\/(\d{1,})/ );

        EspApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount.id = response.data.id;
            self.currentAccount.accountName = response.data.account_name;
            self.currentAccount.key1 = response.data.key_1;
            self.currentAccount.key2 = response.data.key_2;
        } )
    }

    self.loadAccounts = function () {
        EspApiService.getAccounts( self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetCurrentAccount = function () {
        self.currentAccount.espId = '';
        self.currentAccount.id = '';
        self.currentAccount.accountName = '';
        self.currentAccount.key1 = '';
        self.currentAccount.key2 = '';
    };

    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.saveNewAccount = function () {
        self.resetFieldErrors();

        EspApiService.saveNewAccount( self.currentAccount , self.saveNewAccountSuccessCallback , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.resetFieldErrors();

        EspApiService.editAccount( self.currentAccount , self.editAccountSuccessCallback , self.editAccountFailureCallback );
    }

    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load ESP Accounts.' );

        self.launchModal();
    }

    self.saveNewAccountSuccessCallback = function ( response ) {
        self.setModalLabel( 'Success' );
        self.setModalBody( 'Successfully saved ESP Account.' );

        self.resetCurrentAccount();

        self.launchModal();
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.loadFieldErrors( 'espId' , response );
        self.loadFieldErrors( 'accountName' , response );
        self.loadFieldErrors( 'key1' , response );
        self.loadFieldErrors( 'key2' , response );
    };

    self.editAccountSuccessCallback = function ( response ) {
        self.setModalLabel( 'Success' );
        self.setModalBody( 'Successfully updated ESP Account.' );

        self.launchModal();
    };

    self.editAccountFailureCallback = function ( response ) {
        self.loadFieldErrors( 'accountName' , response );
        self.loadFieldErrors( 'key1' , response );
        self.loadFieldErrors( 'key2' , response );
    };

    /**
     * Page Modal
     */

    self.setModalLabel = function ( labelText ) {
        var modalLabel = angular.element( document.querySelector( '#pageModalLabel' ) );

        modalLabel.text( labelText );
    };

    self.setModalBody = function ( bodyText ) {
        var modalBody = angular.element( document.querySelector( '#pageModalBody' ) );

        modalBody.text( bodyText );
    }

    self.launchModal = function () {
        $( '#pageModal' ).modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel( '' );
        self.setModalBody( '' );

        $( '#pageModal' ).modal('hide');
    };

    /**
     * Errors
     */
    self.loadFieldErrors = function ( field , response ) {
        if ( typeof( response.data[ field ] ) != 'undefined' ) {
            self.setFieldError( field , response.data[ field ].join( ' ' ) );
        }
    }

    self.setFieldError = function ( field , errorMessage ) {
        self.formErrors[ field ] = errorMessage;
    }

    self.resetFieldErrors = function () {
        self.setFieldError( 'espId' , '' ); 
        self.setFieldError( 'accountName' , '' ); 
        self.setFieldError( 'key1' , '' );
        self.setFieldError( 'key2' , '' );
    };
} ] );

mt2App.service( 'EspApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/esp';

    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    }

    self.getAccounts = function ( successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback , failureCallback );
    }

    self.saveNewAccount = function ( newAccount , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newAccount
        } ).then( successCallback , failureCallback );
    }

    self.editAccount = function ( account , successCallback , failureCallback  ) {
        var request = account;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    }
} );

//# sourceMappingURL=esp.js.map
