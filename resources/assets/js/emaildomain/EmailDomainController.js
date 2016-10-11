mt2App.controller( 'EmailDomainController' , [ '$log' , '$window' , '$location' , '$timeout' , 'EmailDomainApiService', '$rootScope','$mdToast','formValidationService','modalService', function ( $log , $window , $location , $timeout , EmailDomainApiService, $rootScope, $mdToast, formValidationService, modalService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];
    self.currentAccount = {  "domain_name" : "" ,"domain_group_id":""};
    self.createUrl = 'isp/create/';
    self.editUrl = 'isp/edit/';

    self.formErrors = "";

    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = '-id';
    self.editForm = false;
    self.queryPromise = null;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/isp\/edit\/(\d{1,})/ );

        EmailDomainApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
        } )
    };

    self.loadProfile = function ($id) {
        EmailDomainApiService.getAccount($id , function ( response ) {
            self.currentAccount = response.data;
        } )
    };
    self.loadAccounts = function () {
        self.queryPromise = EmailDomainApiService.getAccounts(self.currentPage , self.paginationCount , self.sort , self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
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
        formValidationService.resetFieldErrors(self);
        self.editForm = true;
        EmailDomainApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        formValidationService.resetFieldErrors(self);
        self.editForm = true;
        EmailDomainApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
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
        $location.url( '/isp' );
        $window.location.href = '/isp';
    };

    self.SuccessProfileCallBackRedirect = function ( response ) {
        $location.url( '/home' );
        $window.location.href = '/home';
    };


    self.toggleRowSuccess = function ( response ) {
        $mdToast.showSimple("ISP Updated");
        self.loadAccounts();
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.editForm = false;
        formValidationService.loadFieldErrors(self,response);
    };

    self.editAccountFailureCallback = function (response ) {
        self.editForm = false;
        formValidationService.loadFieldErrors(self,response);
    };

} ] );
