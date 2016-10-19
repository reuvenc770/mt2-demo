mt2App.service( 'ClientGroupApiService' , function ( $http ) {
    var self = this;

    self.pagerApiUrl = '/api/pager/ClientGroup';
    self.mt1ApiUrl = '/api/mt1/clientgroup';
    self.baseApiUrl = '/api/clientgroup';

    self.getClientGroup = function ( groupId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.mt1ApiUrl + '/' + groupId
        } ).then( successCallback , failureCallback );
    };

    self.getClientGroups = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = { 'field' : sortField , 'desc' : false };

        if (/^\-/.test( sortField ) ) {
            sort.field = sort.field.substring( 1 );
            sort.desc = true;
        }

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , "sort" : sort }
        } ).then( successCallback , failureCallback );
    };

    self.getAllClientGroups = function ( successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + "/all"
        } ).then( successCallback , failureCallback );
    };

    self.getClients = function ( groupID , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.mt1ApiUrl + '/clients/' + groupID
        } ).then( successCallback , failureCallback );
    };

    self.createClientGroup = function ( groupData , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : self.baseApiUrl ,
            "data" : groupData
        } ).then( successCallback , failureCallback );
    };

    self.updateClientGroup = function ( groupData , successCallback ,failureCallback ) {
        $http( {
            "method" : "PUT" ,
            "url" : self.baseApiUrl + '/' + groupData.gid ,
            "params" : { "_method" : "PUT" } ,
            "data" : groupData
        } ).then( successCallback , failureCallback );
    };

    self.copyClientGroup = function ( groupId , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.baseApiUrl + '/copy/' + groupId ,
        } ).then( successCallback , failureCallback );
    };

    self.deleteClientGroup = function ( groupId , successCallback , failureCallback ) {
        $http( {
            "method" : "DELETE" ,
            "url" : self.baseApiUrl + '/' + groupId
        } ).then( successCallback , failureCallback );
    };
} );
