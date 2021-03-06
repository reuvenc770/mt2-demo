mt2App.controller( 'roleController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RoleApiService', '$rootScope' , 'ivhTreeviewMgr' , 'ivhTreeviewBfs' , 'formValidationService' , 'modalService' , function ( $log , $window , $location , $timeout , RoleApiService, $rootScope , ivhTreeviewMgr , ivhTreeviewBfs , formValidationService , modalService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Actions' , 'ID', 'Slug', 'Name'];
    self.currentRole = { "name" : "" ,"apiUser" : false, "permissions" : []};
    self.createUrl = 'role/create/';
    self.editUrl = 'role/edit/';

    self.permissionTree = [];

    self.formErrors = "";
    self.formSubmitted = false;

    self.customTreeTemplate = [
        '<div>',
          '<span ivh-treeview-toggle>',
            '<span ivh-treeview-twistie></span>',
          '</span>',
          '<span ng-if="depth < 2" ivh-treeview-checkbox></span>',
          '<span class="ivh-treeview-node-label" ivh-treeview-toggle>',
            '{{:: trvw.label(node)}}',
          '</span>',
          '<div ivh-treeview-children></div>',
        '<div>'
    ].join('\n');

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
                node.value
                && node.selected
            ) {
                var featurePermissions = node.value;

                for ( i=0 ; i < featurePermissions.length ; i++) {
                  self.currentRole.permissions.push( featurePermissions[i] );
                }
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
        $timeout( function () { $(function () { $('[data-toggle="tooltip"]').tooltip() } ); } , 1500 );

        self.roles = response.data;
    };

    self.loadRolesFailureCallback = function ( response ) {
        modalService.simpleToast( 'Failed to load roles.' );
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
