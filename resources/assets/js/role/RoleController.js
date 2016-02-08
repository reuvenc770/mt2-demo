mt2App.controller( 'roleController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RoleApiService' , function ( $log , $window , $location , $timeout , RoleApiService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Actions' , 'ID', 'Slug', 'Name'];
    self.currentRole = { "name" : "" ,"permissions" : []};
    self.createUrl = 'role/create/';
    self.editUrl = 'role/edit/';

    self.formErrors = "";

    self.loadRole = function () {
        var pathMatches = $location.path().match( /^\/role\/edit\/(\d{1,})/ );

        RoleApiService.getRole( pathMatches[ 1 ] , function ( response ) {
            self.currentRole = response.data;
        } )
    }

    self.loadRoles = function () {
        RoleApiService.getRoles( self.loadRolesSuccessCallback , self.loadRolesFailureCallback );
    };

    self.resetForm = function () {
        self.currentRole = {};
    };

    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $window.location.href = self.createUrl;
    };

    self.saveNewRole = function () {
        self.resetFieldErrors();

        RoleApiService.saveNewRole( self.currentRole , self.SuccessCallBackRedirect , self.saveNewRoleFailureCallback );
    };

    self.editRole = function () {
        self.resetFieldErrors();

        RoleApiService.editRole( self.currentRole , self.SuccessCallBackRedirect , self.editRoleFailureCallback );
    };

    self.toggleSelection = function (role) {
        var idx = self.currentRole.permissions.indexOf(role);

        // is currently selected
        if (idx > -1) {
            self.currentRole.permissions.splice(idx, 1);
        }

        // is newly selected
        else {
            self.currentRole.permissions.push(role);
        }
    };


    /**
     * Callbacks
     */
    self.loadRolesSuccessCallback = function ( response ) {
        self.roles = response.data;
    };

    self.loadRolesFailureCallback = function ( response ) {
        self.setModalLabel( 'Error' );
        self.setModalBody( 'Failed to load Users.' );

        self.launchModal();
    }

    self.SuccessCallBackRedirect = function ( response ) {
        $window.location.href = '/role';
    };

    self.saveNewRoleFailureCallback = function ( response ) {
        self.loadFieldErrors( 'name' , response );
        self.loadFieldErrors( 'permissions' , response );
    };

    self.editRoleFailureCallback = function ( response ) {
        self.loadFieldErrors( 'name' , response );
        self.loadFieldErrors( 'permissions' , response );
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
