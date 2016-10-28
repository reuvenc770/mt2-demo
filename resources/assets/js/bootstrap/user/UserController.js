mt2App.controller( 'userController' , [ '$log' , '$window' , '$location' , '$timeout' , 'UserApiService' , '$mdToast' , 'formValidationService' , 'modalService' , function ( $log , $window , $location , $timeout , UserApiService , $mdToast , formValidationService , modalService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ '' , 'ID', 'email', "username", 'First Name', 'Last Name', 'Roles', "Status" , "Last Login"];
    self.accounts = [];
    self.currentAccount = { "email" : "" , "username": "", "password" : "",  "new_password" : "" , "password_confirmation" : "" , "first_name" : "" , "last_name" : "" , "roles" : ""};
    self.currentAccount.roles = [];
    self.createUrl = 'user/create/';
    self.editUrl = 'user/edit/';
    self.editForm = false;

    self.formErrors = "";

    self.loadAccount = function () {
        var pathMatches = $location.path().match( /^\/user\/edit\/(\d{1,})/ );

        UserApiService.getAccount( pathMatches[ 1 ] , function ( response ) {
            self.currentAccount = response.data;
        } )
    };
    self.loadProfile = function ($id) {

        UserApiService.getAccount($id , function ( response ) {
            self.currentAccount = response.data;
        } )
    };

    self.loadAccounts = function () {
        UserApiService.getAccounts( self.loadAccountsSuccessCallback , self.loadAccountsFailureCallback );
    };

    self.resetForm = function () {
        self.currentAccount = {};
    };

    /**
     * Click Handlers
     */
    self.saveNewAccount = function () {
        self.editForm = true;
        formValidationService.resetFieldErrors(self);

        UserApiService.saveNewAccount( self.currentAccount , self.SuccessCallBackRedirect , self.saveNewAccountFailureCallback );
    };

    self.editAccount = function () {
        self.editForm = true;
        formValidationService.resetFieldErrors(self);

        UserApiService.editAccount( self.currentAccount , self.SuccessCallBackRedirect , self.editAccountFailureCallback );
    };

    self.updateProfile = function () {
        self.editForm = true;
        formValidationService.resetFieldErrors(self);

        UserApiService.updateProfile( self.currentAccount , self.SuccessProfileCallBackRedirect , self.editAccountFailureCallback);
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
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );

        self.accounts = response.data;
    };

    self.loadAccountsFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load Users.' );

        modalService.launchModal();
    };

    self.SuccessCallBackRedirect = function ( response ) {
        $location.url( '/user' );
        $window.location.href = '/user';
    };

    self.SuccessProfileCallBackRedirect = function ( response ) {
        $location.url( '/home' );
        $window.location.href = '/home';
    };

    self.saveNewAccountFailureCallback = function ( response ) {
        self.editForm = false;
        formValidationService.loadFieldErrors(self, response);
    };

    self.editAccountFailureCallback = function ( response ) {
        self.editForm = false;
        formValidationService.loadFieldErrors(self, response);
    };

} ] );
