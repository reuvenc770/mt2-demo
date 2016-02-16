mt2App.service( 'ClientApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/client';

    self.getClient = function ( id , successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback , failureCallback );
    };

    self.getClients = function ( successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl } )
            .then( successCallback , failureCallback );
    };

    self.saveClient = function ( clientData , successCallback , failureCallback ) {
        $http( { "method" : "POST" , "url" : this.baseApiUrl , "data" : clientData } )
            .then( successCallback , failureCallback );
    };

    self.updateClient = function ( clientData , successCallback , failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + clientData.client_id ,
            "params" : { "_method" : "PUT" } ,
            "data" : clientData
        } ).then( successCallback , failureCallback );
    };
} );
