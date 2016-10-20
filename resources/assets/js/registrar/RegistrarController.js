mt2App.controller( 'RegistrarController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RegistrarApiService' ,'$rootScope', '$mdToast', function ( $log , $window , $location , $timeout , RegistrarApiService, $rootScope, $mdToast ) {
    var self = this;
    self.$location = $location;
    self.headers = [ '' , 'ID', 'name', "Username" ];
    self.accounts = [];
    self.currentAccount = { "id": "",
                            "username": "",
                            "contact_name":"",
                            "contact_email":"",
                            "phone_number":"",
                            "address": "",
                            "address_2" : "",
                            "city" : "",
                            "state" : "",
                            "zip" : "",
                            "entity_name":"",};
    self.createUrl = 'registrar/create/';
    self.editUrl = 'registrar/edit/';
    self.pageType = 'add';

    self.formErrors = "";
    self.formsubmitted = false;
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
    self.viewAdd = function () {
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.saveNewAccount = function ( event , form ) {
        self.formsubmitted = true;
        self.resetFieldErrors();

        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {
            field.$setDirty();
            field.$setTouched();

            if ( field.$name == 'state' ) {
                form.state.$error.required = true;
            }

            errorFound = true;
        } );

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        };

        self.currentAccount.status = 1;
        RegistrarApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , function (response) {
            angular.forEach( response.data , function( error , fieldName ) {

                form[ fieldName ].$setDirty();
                form[ fieldName ].$setTouched();
                form[ fieldName ].$setValidity('isValid' , false);
            });

            self.saveNewAccountFailureCallback(response);
        });
    };

    self.editAccount = function () {
        self.resetFieldErrors();

        RegistrarApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };


    /**
     * Watchers
     */
    $rootScope.$on( 'updatePage' , function () {
        self.loadAccounts();
    } );




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
        self.setModalBody( 'Failed to load Users.' );

        self.launchModal();
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
        self.loadFieldErrors(response);
        self.formsubmitted = false;
    };

    self.editAccountFailureCallback = function ( response ) {
        self.loadFieldErrors(response);
    };

    self.toggleRowSuccess = function ( response ) {
        $mdToast.showSimple("Registrar Updated");
        self.loadAccounts();
    };


    /**
     * Errors
     */
    self.loadFieldErrors = function (response ) {
        angular.forEach(response.data, function(value, key) {
            self.setFieldError( key , value );
        });
    };

    self.setFieldError = function ( field , errorMessage ) {
        self.formErrors[ field ] = errorMessage;
    };

    self.resetFieldErrors = function () {
        self.formErrors = {};
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
    };

    self.launchModal = function () {
        $( '#pageModal' ).modal('show');
    };

    self.resetModal = function () {
        self.setModalLabel( '' );
        self.setModalBody( '' );

        $( '#pageModal' ).modal('hide');
    };
} ] );
