mt2App.controller( 'roleController' , [ '$log' , '$window' , '$location' , '$timeout' , 'RoleApiService' , function ( $log , $window , $location , $timeout , RoleApiService ) {
    var self = this;
    self.$location = $location;

    self.headers = [ 'Actions' , 'ID', 'Slug', 'Name'];
    self.currentRole = { "name" : "" ,"apiUser" : false, "permissions" : []};
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

    self.toggleSelection = function (role) {
        var apiRoleAction = convertRoleForApi(role);
        var roleStub = role.substring(0, role.lastIndexOf('.'));
        var apiRole = "api." + roleStub + apiRoleAction;
        var idx = self.currentRole.permissions.indexOf(role);
        var idxAPI = self.currentRole.permissions.indexOf(apiRole);
        // is currently selected
        if (idx > -1) {
            self.currentRole.permissions.splice(idx, 1);

            if(!self.currentRole.apiUser){

                if (apiRoleAction.indexOf(',') > -1){
                    roles = apiRoleAction.split(',');

                    roleAPI = "api." + roleStub + roles[0];

                    idxAPI = self.currentRole.permissions.indexOf(roleAPI);
                    self.currentRole.permissions.splice(idxAPI, 1);

                    roleAPI2 = "api." + roleStub + roles[1];
                    idxAPI2 = self.currentRole.permissions.indexOf(roleAPI2);
                    self.currentRole.permissions.splice(idxAPI2, 1);

                } else {

                    self.currentRole.permissions.splice(idxAPI, 1);
                }
            }
        }

        // is newly selected
        else {
            self.currentRole.permissions.push(role);

            if(!self.currentRole.apiUser){
                if (apiRoleAction.indexOf(',') > -1) {
                    roles = apiRoleAction.split(',');

                    roleAPI = "api." + roleStub + roles[0];
                    self.currentRole.permissions.push(roleAPI);
                    roleAPI2 = "api." + roleStub + roles[1];
                    self.currentRole.permissions.push(roleAPI2);

                } else {
                    self.currentRole.permissions.push(apiRole);
                }
            }
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
        $location.url( '/role' );
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

function convertRoleForApi(role) {
    splitRole = role.split(/[. ]+/);
    $convertedArray = {"list": ".index", "edit": ".update,.show", "add": ".create"};
    action = splitRole.pop();
    return $convertedArray[action];
}
mt2App.service( 'RoleApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/role';

    self.getRole = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    }

    self.getRoles = function ( successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback , failureCallback );
    }

    self.saveNewRole = function ( newRole , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newRole
        } ).then( successCallback , failureCallback );
    }

    self.editRole = function ( role , successCallback , failureCallback  ) {
        var request = role;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + role.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    }
} );

//# sourceMappingURL=role.js.map
