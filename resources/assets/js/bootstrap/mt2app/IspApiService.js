mt2App.service( 'IspApiService' , function ( $http ) {
    var self = this;

    self.apiUrl = '/api/isp';

    self.getAll = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.apiUrl
        } ).then( successCallback , failureCallback );
    };
} );
