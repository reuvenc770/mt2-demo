mt2App.service( 'ClientApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/client';
    self.baseMt1ApiUrl = '/api/mt1';

    self.getClient = function ( id , successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback , failureCallback );
    };

    self.getClients = function ( page , count , successCallback , failureCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/pager' , "params" : { "page" : page , "count" : count } } )
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

    self.getTypes = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseMt1ApiUrl + '/client/types'
        } ).then( successCallback , failureCallback );
    };
} );
