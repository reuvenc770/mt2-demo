mt2App.controller( 'EmailDomainController' , [ '$log' , '$window' , '$location' , '$timeout' , 'EmailDomainApiService', '$rootScope','formValidationService','modalService', 'paginationService' , function ( $log , $window , $location , $timeout , EmailDomainApiService, $rootScope, formValidationService, modalService , paginationService ) {
    var self = this;
    self.$location = $location;

    self.accounts = [];
    self.currentAccount = {  "domain_name" : "" ,"domain_group_id":""};
    self.createUrl = 'isp/create/';
    self.editUrl = 'isp/edit/';

    self.formErrors = "";
    self.search = {};

    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = 'domain_name';
    self.editForm = false;
    self.queryPromise = null;
    self.recordListStatus = 'index';

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

    self.sortCurrentRecords = function () {
        if (self.recordListStatus === 'index' ) {
            self.loadAccounts();
        }

        if ( self.recordListStatus === 'search' ) {
            self.searchDomain();
        }
    };

    /**
     * Click Handlers
     */

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

    self.searchDomain = function () {
        self.recordListStatus = 'search';

        var searchObj = {
            "domainGroupId" : self.search.domain_group_id || undefined
        };

        self.queryPromise = EmailDomainApiService.searchDomain( self.paginationCount , searchObj , self.sort , self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetSearch = function() {
        self.loadAccounts();
        self.search = {};
        self.recordListStatus = 'index';
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
        modalService.simpleToast( 'Failed to load accounts.' );
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
        modalService.setModalLabel('Success');
        modalService.setModalBody("ISP status updated.");
        modalService.launchModal();
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
