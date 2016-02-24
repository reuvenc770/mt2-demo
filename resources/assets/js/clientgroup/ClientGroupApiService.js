mt2App.service( 'ClientGroupApiService' , function ( $http ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/clientgroup';
    self.baseApiUrl = '/api/mt1/clientgroup';

    self.getClientGroups = function ( page , count , successCallback , failureCallback ) {

        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
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
