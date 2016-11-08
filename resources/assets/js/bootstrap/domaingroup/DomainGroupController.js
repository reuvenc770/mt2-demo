mt2App.controller( 'DomainGroupController' , [ '$log' , '$window' , '$location' , '$timeout' , 'DomainGroupApiService', '$rootScope','$mdToast', 'formValidationService','modalService', function ( $log , $window , $location , $timeout , DomainGroupApiService, $rootScope, $mdToast, formValidationService, modalService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];
    self.currentAccount = {  "name" : "" ,"country":"", "status":""};
    self.createUrl = 'ispgroup/create/';
    self.editUrl = 'ispgroup/edit/';

    self.formErrors = "";

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = 'name';
    self.editForm = false;
    self.queryPromise = null;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/ispgroup\/edit\/(\d{1,})/ );

        DomainGroupApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
        } )
    };

    self.loadProfile = function ($id) {

        DomainGroupApiService.getAccount($id , function ( response ) {
            self.currentAccount = response.data;
        } )
    };

    self.loadAccounts = function () {
        self.queryPromise = DomainGroupApiService.getAccounts(self.currentPage , self.paginationCount , self.sort , self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetForm = function () {
        self.currentAccount = {};
    };


    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.saveNewAccount = function () {
        self.editForm = true;
        formValidationService.resetFieldErrors(self);
        self.currentAccount.status = "Active";
        DomainGroupApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.editForm = true;
        formValidationService.resetFieldErrors(self);
        DomainGroupApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.toggle = function( recordId , direction ) {
        DomainGroupApiService.toggleRow( recordId , direction , self.toggleRowSuccess , self.toggleRowFailure );
    }
    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );

        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load accounts.' );
        modalService.launchModal();
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/ispgroup' );
        $window.location.href = '/ispgroup';
    };

    self.SuccessProfileCallBackRedirect = function ( response ) {
        $location.url( '/home' );
        $window.location.href = '/home';
    };


    self.toggleRowSuccess = function ( response ) {
        $mdToast.showSimple("ISP Group Updated");
        self.loadAccounts();
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.editForm = false;
        formValidationService.loadFieldErrors(self,response);
    };

    self.editAccountFailureCallback = function ( response ) {
        self.editForm = false;
        formValidationService.loadFieldErrors(self,response);
    };

} ] );
