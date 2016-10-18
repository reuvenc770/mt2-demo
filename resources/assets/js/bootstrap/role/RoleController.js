mt2App.controller( 'roleController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RoleApiService', '$rootScope' , 'ivhTreeviewMgr' , 'ivhTreeviewBfs' , '$mdToast' , 'formValidationService' , 'modalService' , function ( $log , $window , $location , $timeout , RoleApiService, $rootScope , ivhTreeviewMgr , ivhTreeviewBfs , $mdToast , formValidationService , modalService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Actions' , 'ID', 'Slug', 'Name'];
    self.currentRole = { "name" : "" ,"apiUser" : false, "permissions" : []};
    self.createUrl = 'role/create/';
    self.editUrl = 'role/edit/';

    self.permissionTree = [];

    self.formErrors = "";
    self.formSubmitted = false;

    self.loadPermissionTree = function () {
        var currentPath = $location.path();
        var pathParts = currentPath.match( new RegExp( /(\d+)/ ) );
        var prepopPage = (
            pathParts !== null
            && angular.isNumber( parseInt( pathParts[ 0 ] ) )
        );
        var roleId = ( prepopPage ? pathParts[ 0 ] : 0 );

        RoleApiService.getPermissionTree( roleId , self.loadPermissionTreeSuccessCallback , self.loadPermissionTreeFailureCallback );
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
            RoleApiService.getRole( pathParts[ 0 ] , self.getRoleSuccessCallback );
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

    self.saveNewRole = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        RoleApiService.saveNewRole( self.currentRole , self.SuccessCallBackRedirect , self.saveNewRoleFailureCallback );
    };

    self.editRole = function () {
        self.formSubmitted = true;
        formValidationService.resetFieldErrors(self);

        RoleApiService.editRole( self.currentRole , self.SuccessCallBackRedirect , self.editRoleFailureCallback );
    };

    /**
     * Callbacks
     */
    self.loadPermissionTreeSuccessCallback = function ( response ) {
        self.permissionTree = response.data;

        ivhTreeviewMgr.validate(self.permissionTree, false);

        self.updateCurrentRolePermissions();
    };

    self.loadPermissionTreeFailureCallback = function ( response ) {
        $log.log( response );
    };

    self.getRoleSuccessCallback = function ( response ) {
        self.currentRole = response.data;
    };

    self.loadRolesSuccessCallback = function ( response ) {
        self.roles = response.data;
    };

    self.loadRolesFailureCallback = function ( response ) {
        modalService.setModalLabel( 'Error' );
        modalService.setModalBody( 'Failed to load roles.' );
        modalService.launchModal();
    };

    self.SuccessCallBackRedirect = function ( response ) {
        if($rootScope.wizard !== undefined){
            $rootScope.wizard.goToNextStep();
        } else {
            $location.url('/role');
            $window.location.href = '/role';
        }
    };

    self.saveNewRoleFailureCallback = function ( response ) {
        formValidationService.loadFieldErrors( self , response );
        self.formSubmitted = false;
    };

    self.editRoleFailureCallback = function ( response ) {
        formValidationService.loadFieldErrors( self , response );
        self.formSubmitted = false;
    };

} ] );
