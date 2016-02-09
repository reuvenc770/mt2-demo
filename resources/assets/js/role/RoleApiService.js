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
