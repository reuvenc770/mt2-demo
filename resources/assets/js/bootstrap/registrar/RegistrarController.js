mt2App.controller( 'RegistrarController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RegistrarApiService' ,'$rootScope', '$mdToast', 'formValidationService', 'modalService', function ( $log , $window , $location , $timeout , RegistrarApiService, $rootScope, $mdToast, formValidationService, modalService ) {
    var self = this;
    self.$location = $location;
    self.accounts = [];
    self.currentAccount = { "id": "",
                            "username": "",
                            "password" : "",
                            "contact_name":"",
                            "contact_email":"",
                            "dba_names": "" };
    self.createUrl = 'registrar/create/';
    self.editUrl = 'registrar/edit/';
    self.pageType = 'add';
    self.dba_name = '';
    self.dba_names = [];

    self.formErrors = "";
    self.formSubmitted = false;
    self.pageCount = 0;
    self.paginationCount = '10';
    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = '-status';
    self.queryPromise = null;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/registrar\/edit\/(\d{1,})/ );

        RegistrarApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
            self.dba_names = self.currentAccount.dba_names.split(',');
        } )
    };
    self.loadProfile = function ($id) {

        RegistrarApiService.getAccount($id , function ( response ) {
            self.currentAccount = response.data;
        } )
    };

    self.loadAccounts = function () {
        self.queryPromise = RegistrarApiService.getAccounts(
            self.currentPage,
            self.paginationCount,
            self.sort,
            self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetForm = function () {
        self.currentAccount = {};
    };


    self.toggle = function(recordId,direction) {
        RegistrarApiService.toggleRow(recordId, direction, self.toggleRowSuccess, self.toggleRowFailure)
    };

    self.setPageType = function(pageType){
        self.pageType = pageType;
    };
    /**
     * Click Handlers
     */
    self.saveNewAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        self.currentAccount.status = 1;
        self.currentAccount.dba_names = self.dba_names.join(', ');
        RegistrarApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback);
    };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        self.currentAccount.dba_names = self.dba_names.join(', ');
        RegistrarApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.addDba = function () {
        if(self.dba_name.length > 0){
            self.dba_names.push(self.dba_name);
            self.dba_name = "";
        }
    };

    self.removeDba = function (id) {
        self.dba_names.splice( id , 1 );

    };
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
        modalService.setModalBody( 'Failed to load Users.' );
        modalService.launchModal();
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/registrar' );
        $window.location.href = '/registrar';
    };

    self.SuccessProfileCallBackRedirect = function ( response ) {
        $location.url( '/home' );
        $window.location.href = '/home';
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        formValidationService.loadFieldErrors(self,response);
        self.formSubmitted = false;
    };

    self.editAccountFailureCallback = function ( response ) {
        self.formSubmitted = false;
        formValidationService.loadFieldErrors(self,response);
    };

    self.toggleRowSuccess = function ( response ) {
        $mdToast.showSimple("Registrar Updated");
        self.loadAccounts();
    };

} ] );
