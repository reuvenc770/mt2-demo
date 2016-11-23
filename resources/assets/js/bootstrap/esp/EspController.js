mt2App.controller( 'espController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'EspService' , 'formValidationService' , 'modalService' , 'paginationService' , function ( $rootScope , $log , $window , $location , $timeout , EspService , formValidationService , modalService , paginationService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];

    self.currentAccount = { "_token" : "" , "id" : "" , "name" : "" , "email_id_field" : "" , "email_address_field" : "" };

    self.editUrl = 'esp/edit/';

    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.accountTotal = 0;
    self.formSubmitted = false;


    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/esp\/edit\/(\d{1,})/ );

        EspService.getAccount( pathMatches[ 1 ] , self.loadAccountSuccesCallback )
    };

    self.loadAccounts = function () {
        EspService.getAccounts(
            self.currentPage ,
            self.paginationCount ,
            self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    /**
     * Click Handlers
     */
     self.saveNewAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        EspService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self. saveNewAccountFailureCallback );
     };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        EspService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    /**
     * Callbacks
     */
    self.loadAccountSuccesCallback = function ( response ) {
        var currentToken = self.currentAccount._token;

        self.currentAccount = response.data;
        self.currentAccount._token = currentToken;

        if ( response.data.field_options != null ) {
            self.currentAccount.email_id_field = response.data.field_options.email_id_field;
            self.currentAccount.email_address_field = response.data.field_options.email_address_field;
        }

    }
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;

        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load ESP Accounts.' );
        modalService.launchModal();
    };


    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/esp' );
        $window.location.href = '/esp';
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };

    self.editAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };


} ] );
