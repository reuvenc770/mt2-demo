mt2App.service( 'ClientGroupApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/mt1/clientgroup';

    self.getClientGroups = function ( page , count , successCallback , failureCallback ) {
        $log.log( 'calling getClientGroups' );

        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/pager' ,
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.getClients = function ( groupID , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/' + groupID
        } ).then( successCallback , failureCallback );
    }
} );
