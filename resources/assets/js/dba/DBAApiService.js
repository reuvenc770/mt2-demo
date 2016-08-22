mt2App.service( 'DBAApiService' , function ( $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/dba';
    self.pagerApiUrl = '/api/pager/DoingBusinessAs';
    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    };

    self.getAccounts = function ( page , count , successCallback , failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count }
        } ).then( successCallback , failureCallback );
    };

    self.saveNewAccount = function ( newAccount , successCallback , failureCallback ) {
        $http( {
            "method" : "POST" ,
            "url" : this.baseApiUrl ,
            "data" : newAccount
        } ).then( successCallback , failureCallback );
    };

    self.editAccount = function ( account , successCallback , failureCallback  ) {
        var request = account;

        request[ '_method' ] = 'PUT';

        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    };

    self.toggleRow = function ( recordId, direction, successCallback, failureCallback ) {
        $http( {
            "method" : "DELETE" ,
            "url" : this.baseApiUrl + '/' + recordId,
            "params" : { "direction" : direction }
        } ).then( successCallback , failureCallback );
    };

} );
