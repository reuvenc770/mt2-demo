mt2App.controller( 'domainController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'DomainService' , function ( $rootScope , $log , $window , $location , $timeout , DomainService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID' , 'ESP' , 'Key 1' , 'Key 2' , 'Account' , 'Created' , 'Updated' ];
    self.accounts = [];

    self.currentAccount = { "domain_type" : "" , "esp_name" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.createUrl = 'espapi/create/';
    self.editUrl = 'espapi/edit/';
    self.espAccounts = [];
    self.formErrors = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.updatingAccounts = false;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/domain\/edit\/(\d{1,})/ );

        DomainService.getAccount( pathMatches[ 1 ] , function ( response ) {

        } )
    }

    self.loadAccounts = function () {
        DomainService.getAccounts(
            self.currentPage ,
            self.paginationCount ,
            self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetCurrentAccount = function () {

    };

    self.updateEspAccounts = function (){
        self.updatingAccounts = true;
        DomainService.getEspAccounts(
            self.currentAccount.espName ,
            self.updateEspAccountsSuccessCallback , self.loadAccountsFailureCallback );
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

        DomainService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.resetFieldErrors();
        DomainService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    /**
     * Callbacks
     */
    self.updateEspAccountsSuccessCallback = function ( response ) {
        self.espAccounts = response.data;
        self.updatingAccounts = false;
    };
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        self.setModalLabel( 'Erro r' );
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
