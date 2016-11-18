mt2App.controller( 'ClientController' , [ '$log' , '$window' , '$location', '$timeout' , 'ClientApiService' , '$rootScope' , '$mdToast' , 'formValidationService' , 'modalService' , function ( $log , $window , $location , $timeout , ClientApiService , $rootScope, $mdToast , formValidationService, modalService) {
    var self = this;

    self.accounts = [];
    self.current = {
        _token : "" ,
        id : "" ,
        name : "" ,
        address : "" ,
        address2 : "" ,
        city : "" ,
        state : "" ,
        zip : "" ,
        email_address : "" ,
        phone : "" ,
        status : ""
    };

    self.createUrl = "client/create";

    self.pageCount = 0;
    self.paginationCount = "10";
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = "-id";
    self.queryPromise = null;
    self.formSubmitted = false;

    self.loadAccounts = function () {
        self.queryPromise = ClientApiService.getAccounts(
            self.currentPage ,
            self.paginationCount ,
            self.sort ,
            self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.setData = function ( currentClient ) {
        self.current = currentClient;
    };

    self.saveClient = function () {
        self.formSubmitted = true;

        ClientApiService.saveClient( self.current , self.successRedirectCallback , self.saveClientFailureCallback );
    };

    self.updateClient = function () {
        self.formSubmitted = true;

        ClientApiService.updateClient( self.current , self.successRedirectCallback , self.updateClientFailureCallback );
    };

    /**
     * Callbacks
     */
    self.loadAccountSuccessCallback = function ( response ) {
        var currentToken = self.current._token;

        self.current = response.data;
        self.current._token = currentToken;
    };

    self.loadAccountsSuccessCallback = function( response ) {
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.loadAccountsFailureCallback = function( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load clients.' );
        modalService.launchModal();
    };

    self.successRedirectCallback = function () {
        $location.url( '/client' );
        $window.location.href = '/client';
    };

    self.saveClientFailureCallback = function( response ) {
        self.formSubmitted = false;

        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to save clients.' );
        modalService.launchModal();
    };

    self.updateClientFailureCallback = function( response ) {
        self.formSubmitted = false;

        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to update clients.' );
        modalService.launchModal();
    };
} ] );
