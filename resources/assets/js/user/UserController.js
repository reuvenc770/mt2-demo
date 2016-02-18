mt2App.controller( 'userController' , [ '$log' , '$window' , '$location' , '$timeout' , 'UserApiService' , function ( $log , $window , $location , $timeout , UserApiService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID', 'email', 'First Name', 'Last Name', 'Roles', "Status" , "Last Login"];
    self.accounts = [];
    self.currentAccount = { "email" : "" , "password" : "" , "password_confirmation" : "" , "first_name" : "" , "last_name" : "" , "roles" : ""};
    self.currentAccount.roles = [];
    self.createUrl = 'user/create/';
    self.editUrl = 'user/edit/';

    self.formErrors = "";

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/user\/edit\/(\d{1,})/ );

        UserApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
        } )
    }

    self.loadAccounts = function () {
        UserApiService.getAccounts( self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
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

        UserApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.resetFieldErrors();

        UserApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.toggleSelection = function (role) {
        var idx = self.currentAccount.roles.indexOf(role);

        // is currently selected
        if (idx > -1) {
            self.currentAccount.roles.splice(idx, 1);
        }

        // is newly selected
        else {
            self.currentAccount.roles.push(role);
        }
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
        $location.url( '/user' );
        $window.location.href = '/user';
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.loadFieldErrors( 'email' , response );
        self.loadFieldErrors( 'first_name' , response );
        self.loadFieldErrors( 'last_name' , response );
        self.loadFieldErrors( 'password' , response );
        self.loadFieldErrors( 'password_confirmation' , response );
        self.loadFieldErrors( 'roles' , response );
    };

    self.editAccountFailureCallback = function ( response ) {
        self.loadFieldErrors( 'email' , response );
        self.loadFieldErrors( 'first_name' , response );
        self.loadFieldErrors( 'last_name' , response );
        self.loadFieldErrors( 'roles' , response );
    };

    /**
     * Errors
     */
    self.loadFieldErrors = function ( field , response ) {
        if ( typeof( response.data[ field ] ) != 'undefined' ) {
            self.setFieldError( field , response.data[ field ].join( ' ' ) );
        }
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
