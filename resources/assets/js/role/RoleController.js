mt2App.controller( 'roleController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RoleApiService', '$rootScope' , 'ivhTreeviewMgr' , 'ivhTreeviewBfs' , '$mdToast' , 'CustomValidationService' , function ( $log , $window , $location , $timeout , RoleApiService, $rootScope , ivhTreeviewMgr , ivhTreeviewBfs , $mdToast , CustomValidationService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Actions' , 'ID', 'Slug', 'Name'];
    self.currentRole = { "name" : "" ,"apiUser" : false, "permissions" : []};
    self.createUrl = 'role/create/';
    self.editUrl = 'role/edit/';

    self.permissionTree = [];

    self.formErrors = "";

    self.loadPermissionTree = function () {
        var currentPath = $location.path();
        var pathParts = currentPath.match( new RegExp( /(\d+)/ ) );
        var prepopPage = (
            pathParts !== null
            && angular.isNumber( parseInt( pathParts[ 0 ] ) )
        );
        var roleId = ( prepopPage ? pathParts[ 0 ] : 0 );

        RoleApiService.getPermissionTree( roleId , function ( response ) {
            self.permissionTree = response.data;

            ivhTreeviewMgr.validate(self.permissionTree, false);

            self.updateCurrentRolePermissions();
        } , function ( response ) { $log.log( response ); } );
    };

    self.updateSelectedPermissions = function () {
        self.currentRole.permissions = [];

        self.updateCurrentRolePermissions();
    };

    self.updateCurrentRolePermissions = function () {
        ivhTreeviewBfs( self.permissionTree , function ( node ) {
            if (
                !node.children
                && node.selected
                && self.currentRole.permissions.indexOf( node.id ) === -1
            ) {
                self.currentRole.permissions.push( node.id );
            }
        } );
    };

    self.initEditPage = function () {
        var currentPath = $location.path();
        var pathParts = currentPath.match( new RegExp( /(\d+)/ ) );
        var prepopPage = (
            pathParts !== null
            && angular.isNumber( parseInt( pathParts[ 0 ] ) )
        );

        if ( prepopPage ) {
            RoleApiService.getRole( pathParts[ 0 ] , function ( response ) {
                self.currentRole = response.data;
            } );
        }
    };

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
        $location.url( self.createUrl );
        $window.location.href = self.createUrl;
    };

    self.change = function ( form , fieldName ) {
        CustomValidationService.onChangeResetValidity( self , form , fieldName );
    };

    self.saveNewRole = function ( event , form ) {
        self.resetFieldErrors();

        var errorFound = false;

        angular.forEach( form.$error.required , function( field ) {
            field.$setDirty();
            field.$setTouched();

            errorFound = true;
        } );

        if ( errorFound ) {
            $mdToast.showSimple( 'Please fix errors and try again.' );

            return false;
        };

        RoleApiService.saveNewRole( self.currentRole , self.SuccessCallBackRedirect , self.saveNewRoleFailureCallback );
    };

    self.editRole = function () {
        self.resetFieldErrors();

        RoleApiService.editRole( self.currentRole , self.SuccessCallBackRedirect , self.editRoleFailureCallback );
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
        if($rootScope.wizard !== undefined){
            $rootScope.wizard.goToNextStep();
        } else {
            $location.url('/role');
            $window.location.href = '/role';
        }
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
            self.setFieldError( field , response.data[ field ] );
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
