mt2App.controller( 'espController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'EspApiService' , function ( $rootScope , $log , $window , $location , $timeout , EspApiService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID' , 'ESP' , 'Key 1' , 'Key 2' , 'Account' , 'Created' , 'Updated' ];
    self.accounts = [];

    self.currentAccount = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.createUrl = 'espapi/create/';
    self.editUrl = 'espapi/edit/';

    self.formErrors = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = '-id';
    self.queryPromise = null;

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
        self.queryPromise = EspApiService.getAccounts(
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
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

    self.saveNewAccount = function () {
        self.resetFieldErrors();

        EspApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.resetFieldErrors();

        EspApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    }

    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
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
