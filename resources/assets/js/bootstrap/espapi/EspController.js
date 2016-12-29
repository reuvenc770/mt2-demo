mt2App.controller( 'espController' , [ '$rootScope' , '$log' , '$window' , '$location' , '$timeout' , 'EspApiService' , 'formValidationService' , 'modalService' , 'paginationService' , function ( $rootScope , $log , $window , $location , $timeout , EspApiService , formValidationService , modalService , paginationService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID' , 'ESP' , 'Key 1' , 'Key 2' , 'Account' , 'Created' , 'Updated' ];
    self.accounts = [];

    self.currentAccount = { "espId" : "" , "id" : "" , "accountName" : "" , "key1" : "" , "key2" : "" };

    self.espApiKeyNameMapping = {
     '2' : { 'key1' : 'Username' , 'key2' : 'Password' },
     '4' : { 'key1' : 'Account' , 'key2' : 'API Key' },
     '9' : { 'key1' : 'Access Token' , 'key2' : false },
     '15' : { 'key1' : 'Access Token' , 'key2' : false },
     '3' : { 'key1' : 'API Key' , 'key2' : false },
     '8' : { 'key1' : 'Username' , 'key2' : 'Password' },
     '5' : { 'key1' : 'Username' , 'key2' : 'Password' },
     '1' : { 'key1' : 'API Key' , 'key2' : 'Shared Secret' },
     '6' : { 'key1' : 'Access Token' , 'key2' : 'Access Secret' },
     '14' : { 'key1' : 'Access Token' , 'key2' : 'Access Secret' }
    };
    self.key1Name = 'Key 1';
    self.key2Name = 'Key 2';

    self.createUrl = 'espapi/create/';
    self.editUrl = 'espapi/edit/';

    self.formErrors = {};

    self.currentlyLoading = 0;
    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = '-id';
    self.queryPromise = null;
    self.formSubmitted = false;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/espapi\/edit\/(\d{1,})/ );

        EspApiService.getAccount( pathMatches[ 1 ] , self.loadAccountSuccessCallback );
    };

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

    self.updateKeyNames = function ( espId ) {
        if ( self.espApiKeyNameMapping.hasOwnProperty(espId) ) {
            self.key1Name = self.espApiKeyNameMapping[espId].key1 || 'Key 1';
            self.key2Name = self.espApiKeyNameMapping[espId].key2 || 'Key 2';
        } else {
            self.key1Name = 'Key 1';
            self.key2Name = 'Key 2';
        }
    };

    /**
     * Click Handlers
     */
    self.saveNewAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        EspApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        EspApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.toggle = function(recordId,direction) {
        EspApiService.toggleRow(recordId, direction, self.toggleRowSuccess, self.toggleRowFailure)
    };

    /**
     * Callbacks
     */
    self.loadAccountSuccessCallback = function ( response ) {
        self.currentAccount.espId = String(response.data.esp_id);
        self.currentAccount.id = response.data.id;
        self.currentAccount.accountName = response.data.account_name;
        self.currentAccount.key1 = response.data.key_1;
        self.currentAccount.key2 = response.data.key_2;

        self.updateKeyNames( self.currentAccount.espId );
    };

    self.loadAccountsSuccessCallback = function ( response ) {
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );

        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load ESP API accounts.' );
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/espapi' );
        $window.location.href = '/espapi';
    };

    self.editAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors( self , response );
    };
    self.toggleRowSuccess = function ( response ) {
        modalService.setModalLabel('Success');
        modalService.setModalBody( response.data );
        modalService.launchModal();
        self.loadAccounts();
    };
    self.toggleRowFailure = function (){
        modalService.setModalLabel('Error');
        modalService.setModalBody( "Failed to update ESP API account status. Please try again." );
        modalService.launchModal();
        self.loadAccounts();
    };

} ] );
