mt2App.controller( 'RegistrarController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RegistrarApiService' ,'$rootScope', 'formValidationService', 'modalService', 'paginationService' , function ( $log , $window , $location , $timeout , RegistrarApiService, $rootScope, formValidationService, modalService , paginationService ) {
    var self = this;
    self.$location = $location;
    self.accounts = [];
    self.currentAccount = { "id": "",
                            "username": "",
                            "password" : "",
                            "dba_names": [],
                            "notes":""};
    self.createUrl = 'registrar/create/';
    self.editUrl = 'registrar/edit/';
    self.pageType = 'add';
    self.currentDba = { 'dba_name' : '' , 'dba_contact_name' : '' , 'dba_contact_email' : '' };
    self.editingDba = false;

    self.formErrors = {};
    self.formSubmitted = false;
    self.pageCount = 0;
    self.paginationCount = paginationService.getDefaultPaginationCount();
    self.paginationOptions = paginationService.getDefaultPaginationOptions();

    self.currentPage = 1;
    self.accountTotal = 0;
    self.sort = '-status';
    self.queryPromise = null;

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/registrar\/edit\/(\d{1,})/ );

        RegistrarApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            response.data.dba_names = angular.fromJson( response.data.dba_names );
            self.currentAccount = response.data;
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
        RegistrarApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback);
    };

    self.editAccount = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);
        RegistrarApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.addDba = function () {
        self.editingDba = false;
        var dbaError = false;

        if(self.currentDba.dba_name == '') {
            self.formErrors.dba_name = [ 'A DBA is required.' ];
            dbaError = true;
        }
        if(self.currentDba.dba_contact_name == '') {
            self.formErrors.dba_contact_name = [ 'A contact name for the DBA is required.' ];
            dbaError = true;
        }
        if(self.currentDba.dba_contact_email == '') {
            self.formErrors.dba_contact_email = [ 'A contact email for the DBA is required.' ];
            dbaError = true;
        }
        if (dbaError) {
            return;
        } else {
            delete( self.formErrors.dba_name );
            delete( self.formErrors.dba_contact_name );
            delete( self.formErrors.dba_contact_email );
        }

        dbas = self.currentAccount.dba_names;
        dbas.push( self.currentDba );
        self.clearDbaFields();
    };

    self.editDba = function (id) {
        self.currentDba = self.currentAccount.dba_names[ id ];
        self.currentAccount.dba_names.splice( id , 1);
        self.editingDba = true;
    }

    self.removeDba = function (id) {
        self.currentAccount.dba_names.splice( id , 1 );

    };

    self.clearDbaFields = function () {
        self.currentDba = { dba_name : '' , dba_contact_name : '' , dba_contact_email : '' };
    }

    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );

        angular.forEach( response.data.data , function ( value , key ) {
            if ( value.dba_names != '' ) {
                response.data.data[ key ].dba_names = angular.fromJson( value.dba_names );
            }
        } );
        self.accounts = response.data.data;
        self.pageCount = response.data.last_page;
        self.accountTotal = response.data.total;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load users.' );
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
        modalService.setModalLabel('Success');
        modalService.setModalBody("Registrar status updated.");
        modalService.launchModal();
        self.loadAccounts();
    };

    self.toggleRowFailure = function ( response ) {
        modalService.setModalLabel('Error');
        modalService.setModalBody('Failed to update registrar status. Please try again.');
        modalService.launchModal();
        self.loadAccounts();
    };

} ] );
