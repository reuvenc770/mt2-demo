mt2App.controller( 'espMappingController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'EspMappingService' , function ( $rootScope , $log , $window , $location , $timeout , EspMappingService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID' , 'Name' , 'Key 1' , 'Key 2' , 'Use Csv?' ];
    self.espAccounts = [];

    self.currentAccount = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.editUrl = 'espapi/edit/';

    self.formErrors = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/espapi\/edit\/(\d{1,})/ );

        EspMappingService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount.id = response.data.id;
            self.currentAccount.accountName = response.data.account_name;
            self.currentAccount.key1 = response.data.key_1;
            self.currentAccount.key2 = response.data.key_2;
        } )
    }

    self.loadEsps = function () {

        EspMappingService.getAccounts(self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetCurrentAccount = function () {
        self.currentAccount.espId = '';
        self.currentAccount.id = '';
        self.currentAccount.accountName = '';
        self.currentAccount.key1 = '';
        self.currentAccount.key2 = '';
    };

    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        self.loadAccounts();
    } );

    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };



    self.editAccount = function () {
        self.resetFieldErrors();

        EspMappingService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    }

    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        console.log("here");
        console.log(response.data);
        self.espAccounts = response.data;

    };

    self.loadAccountsFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load ESP Accounts.' );

        self.launchModal();
    }

    self.saveNewAccountFailureCallback = function ( response ) {
        self.loadFieldErrors( 'espId' , response );
        self.loadFieldErrors( 'accountName' , response );
        self.loadFieldErrors( 'key1' , response );
        self.loadFieldErrors( 'key2' , response );
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/espapi' );
        $window.location.href = '/espapi';
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
