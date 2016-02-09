mt2App.service( 'ClientApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/client';

    self.getClients = function ( successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback , failureCallback );
    }
} );
