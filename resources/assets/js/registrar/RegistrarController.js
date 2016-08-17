mt2App.controller( 'RegistrarController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RegistrarApiService' , function ( $log , $window , $location , $timeout , RegistrarApiService ) {
    var self = this;
    self.$location = $location;
    self.headers = [ '' , 'ID', 'name', "Username" ];
    self.accounts = [];
    self.currentAccount = { "id": "",
                            "name" : "" ,
                            "username": "",
                            "contact_name":"",
                            "contact_email":"",
                            "phone_number":"",
                            "address": "",
                            "address_2" : "",
                            "city" : "",
                            "state" : "",
                            "zip" : "",
                            "entity_name":""};
    self.createUrl = 'registrar/create/';
    self.editUrl = 'registrar/edit/';

    self.formErrors = "";

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
        RegistrarApiService.getAccounts( self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
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
        self.resetFieldErrors();
        self.currentAccount.status = 1;
        RegistrarApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.resetFieldErrors();

        RegistrarApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };




    /**
     * Callbacks
     */
    self.loadAccountsSuccessCallback = function ( response ) {
        self.accounts = response.data;
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
    };

    self.editAccountFailureCallback = function ( response ) {
        self.loadFieldErrors(response);
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
