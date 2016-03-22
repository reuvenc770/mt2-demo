mt2App.controller( 'roleController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RoleApiService', '$rootScope' , function ( $log , $window , $location , $timeout , RoleApiService, $rootScope ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Actions' , 'ID', 'Slug', 'Name'];
    self.currentRole = { "name" : "" ,"apiUser" : false, "permissions" : []};
    self.createUrl = 'role/create/';
    self.editUrl = 'role/edit/';

    self.permissions = [];
    $rootScope.selectedPermissions = {};
    self.defaultPermissions = [ 'home' , 'login' , 'logout' , 'forget.getemail' , 'forget.postemail' , 'pager' , 'myprofile' , 'profile.update' , 'password.reset' , 'password.store' , 'sessions.create' , 'sessions.destroy' , 'sessions.store' ];

    self.formErrors = "";

    self.initEditPage = function () {
        var currentPath = $location.path();
        var pathParts = currentPath.match( new RegExp( /(\d+)/ ) );
        var prepopPage = (
            pathParts !== null
            && angular.isNumber( parseInt( pathParts[ 0 ] ) )
        );

        self.loadPermissions();

        if ( prepopPage ) {
            RoleApiService.getRole( pathParts[ 0 ] , function ( response ) {
                self.currentRole = response.data;

                angular.forEach( response.data.permissions , function ( value , key ) {
                    $rootScope.selectedPermissions[ value ] = true;
                } );
            } );
        }
    };

    self.initCreatePage = function () {
        self.loadPermissions();

        angular.forEach( self.defaultPermissions , function ( value , key ) {
            $rootScope.selectedPermissions[ value ] = true;
        } );
    };

    self.loadRoles = function () {
        RoleApiService.getRoles( self.loadRolesSuccessCallback , self.loadRolesFailureCallback );
    };

    self.resetForm = function () {
        self.currentRole = {};
    };

    self.loadPermissions = function () {
        self.permissions = RoleApiService.getPermissions( self.loadPermissionsSuccessCallback , self.loadPermissionsFailureCallback );
    };

    self.loadPermissionsSuccessCallback = function ( response ) {
        $log.log( response );

        self.permissions = response.data;
    };

    self.loadPermissionsFailureCallback = function ( response ) {
        $log.log( response );
    };

    /**
     * Click Handlers
     */
    self.viewAdd = function () {
        $location.url( self.createUrl );
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

    self.selectPermissions = function ( permissionList ) {
        if ( angular.isArray( permissionList ) ) {
            angular.forEach( permissionList , function ( permissionName , key ) {
                $rootScope.selectedPermissions[ permissionName ] = true;
            } );
        } else {
            angular.forEach( permissionList , function ( value , groupKey ) {
                angular.forEach( value , function ( permissionName , key ) {
                    $rootScope.selectedPermissions[ permissionName ] = true;
                } );
            } );
        }
    }

    self.unselectPermissions = function ( permissionList ) {
        if ( angular.isArray( permissionList ) ) {
            angular.forEach( permissionList , function ( permissionName , key ) {
                $rootScope.selectedPermissions[ permissionName ] = false;
            } );
        } else {
            angular.forEach( permissionList , function ( value , groupKey ) {
                angular.forEach( value , function ( permissionName , key ) {
                    $rootScope.selectedPermissions[ permissionName ] = false;
                } );
            } );
        }
    }

    
    /**
     * Watchers
     */
    $rootScope.$watchCollection( 'selectedPermissions' , function ( newPermissions , oldPermissions ) {
        self.currentRole.permissions = [];

        angular.forEach( newPermissions , function ( value , key ) {
            if ( value === true ) self.currentRole.permissions.push( key );
        });
    } );

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
