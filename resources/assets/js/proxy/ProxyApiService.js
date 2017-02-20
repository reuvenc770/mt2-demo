mt2App.service( 'ProxyApiService' , [ 'paginationService' , '$http' , '$log' , function ( paginationService , $http , $log ) {
    var self = this;

    self.baseApiUrl = '/api/proxy';
    self.pagerApiUrl = '/api/pager/Proxy';
    self.getAccount = function ( id , successCallback ) {
        $http( { "method" : "GET" , "url" : this.baseApiUrl + '/' + id } )
            .then( successCallback );
    };

    self.getAccounts = function ( page , count , sortField , successCallback , failureCallback ) {
        var sort = paginationService.sortPage( sortField );

        return $http( {
            "method" : "GET" ,
            "url" : self.pagerApiUrl ,
            "params" : { "page" : page , "count" : count , "sort" : sort }
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
        $http( {
            "method" : "PUT" ,
            "url" : this.baseApiUrl + '/' + account.id ,
            "data" : request
        } ).then( successCallback , failureCallback );
    };

    self.deleteRow = function ( recordId, successCallback, failureCallback ) {
        $http( {
            "method" : "DELETE" ,
            "url" : this.baseApiUrl + '/' + recordId,
        } ).then( successCallback , failureCallback );
    };
    self.toggleRow = function ( recordId, direction, successCallback, failureCallback ) {
        $http( {
            "method" : "GET" ,
            "url" : this.baseApiUrl + '/toggle/' + recordId,
            "params" : { "direction" : direction }
        } ).then( successCallback , failureCallback );
    };

} ] );
